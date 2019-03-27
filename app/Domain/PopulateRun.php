<?php declare(strict_types=1);

namespace App\Domain;

use App\Domain\Services\Efetch;

final class PopulateRun
{
    const NOT_FOUND = 0;
    const ALREADY_POPULATED = 1;
    const UPDATE_SUCCESS = 2;
    const EFETCH_ERROR = 3;
    const SOME_FAILED = 4;

    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs WHERE id = ?
SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT p.*
        FROM publications AS p, associations AS a
        WHERE p.pmid = a.pmid AND a.run_id = ?
        AND p.populated IS FALSE
SQL;

    const UPDATE_RUN_SQL = <<<SQL
        UPDATE runs SET populated = TRUE WHERE id = ?
SQL;

    const UPDATE_PUBLICATION_SQL = <<<SQL
        UPDATE publications
        SET populated = TRUE, metadata = ?
        WHERE pmid = ?
SQL;

    private $pdo;

    private $efetch;

    public function __construct(\PDO $pdo, Efetch $efetch)
    {
        $this->pdo = $pdo;
        $this->efetch = $efetch;
    }

    public function __invoke(int $id): \Generator
    {
        // prepare the queries.
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);
        $update_run_sth = $this->pdo->prepare(self::UPDATE_RUN_SQL);
        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);

        // select the curation run.
        $select_run_sth->execute([$id]);

        if (! $run = $select_run_sth->fetch()) {
            yield new DomainPayload(self::NOT_FOUND);

            return;
        }

        if ($run['populated']) {
            yield new DomainPayload(self::ALREADY_POPULATED);

            return;
        }

        // select the non populated publications of the curation run.
        $errors = 0;

        $select_publications_sth->execute([$run['id']]);

        while ($publication = $select_publications_sth->fetch()) {
            $result = $this->efetch->metadata($publication['pmid']);

            if ($result['success']) {
                $update_publication_sth->execute([
                    $result['data']['json'],
                    $publication['pmid']
                ]);

                yield new DomainPayload(self::UPDATE_SUCCESS, [
                    'pmid' => $publication['pmid'],
                ]);
            } else {
                $errors++;

                yield new DomainPayload(self::EFETCH_ERROR, array_merge($result['data'], [
                    'pmid' => $publication['pmid'],
                ]));
            }
        }

        // return a failure when there is errors.
        if ($errors > 0) {
            yield new DomainPayload(self::SOME_FAILED, ['errors' => $errors]);

            return;
        }

        // update the curation run state when no error happened.
        $update_run_sth->execute([$run['id']]);

        // success !
        yield new DomainSuccess;
    }
}
