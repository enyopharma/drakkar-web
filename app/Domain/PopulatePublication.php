<?php declare(strict_types=1);

namespace App\Domain;

use App\Domain\Services\Efetch;

final class PopulatePublication
{
    const NOT_FOUND = 0;
    const ALREADY_POPULATED = 1;
    const EFETCH_ERROR = 2;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT r.*
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND a.publication_id = ?
SQL;

    const SELECT_PUBLICATION_SQL = <<<SQL
        SELECT * FROM publications WHERE pmid = ?
SQL;

    const COUNT_NOT_POPULATED_SQL = <<<SQL
        SELECT COUNT(*)
        FROM publications AS p, associations AS a
        WHERE p.id = a.publication_id
        AND a.run_id = ?
        AND p.populated IS FALSE
SQL;

    const UPDATE_RUN_SQL = <<<SQL
        UPDATE runs SET populated = TRUE WHERE id = ?
SQL;

    const UPDATE_PUBLICATION_SQL = <<<SQL
        UPDATE publications
        SET populated = TRUE, metadata = ?
        WHERE id = ?
SQL;

    private $pdo;

    private $efetch;

    public function __construct(\PDO $pdo, Efetch $efetch)
    {
        $this->pdo = $pdo;
        $this->efetch = $efetch;
    }

    public function __invoke(int $pmid): DomainPayloadInterface
    {
        // prepare the queries.
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);
        $select_publication_sth = $this->pdo->prepare(self::SELECT_PUBLICATION_SQL);
        $count_not_populated_sth = $this->pdo->prepare(self::COUNT_NOT_POPULATED_SQL);
        $update_run_sth = $this->pdo->prepare(self::UPDATE_RUN_SQL);
        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);

        // select the publication.
        $select_publication_sth->execute([$pmid]);

        if (! $publication = $select_publication_sth->fetch()) {
            return new DomainPayload(self::NOT_FOUND);
        }

        if ($publication['populated']) {
            return new DomainPayload(self::ALREADY_POPULATED);
        }

        // download the metadata.
        $result = $this->efetch->metadata($publication['pmid']);

        // return error.
        if (! $result['success']) {
            return new DomainPayload(self::EFETCH_ERROR, $result['data']);
        }

        // update publication.
        $this->pdo->beginTransaction();

        $update_publication_sth->execute([
            $result['data']['json'],
            $publication['id'],
        ]);

        $select_runs_sth->execute([$publication['id']]);

        while ($run = $select_runs_sth->fetch()) {
            $count_not_populated_sth->execute([$run['id']]);

            if (! $count_not_populated_sth->fetchColumn()) {
                $update_run_sth->execute([$run['id']]);
            }
        }

        $this->pdo->commit();

        // success !
        return new DomainSuccess;
    }
}
