<?php

declare(strict_types=1);

namespace Domain\Actions;

use Services\Efetch;

use Domain\Publication;
use Domain\Payloads\RuntimeFailure;
use Domain\Payloads\DomainConflict;
use Domain\Payloads\ResourceUpdated;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class PopulatePublication
{
    const SELECT_RUNS_SQL = <<<SQL
        SELECT r.*
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND a.pmid = ?
SQL;

    const SELECT_PUBLICATION_SQL = <<<SQL
        SELECT * FROM publications WHERE pmid = ?
SQL;

    const COUNT_NOT_POPULATED_SQL = <<<SQL
        SELECT COUNT(*)
        FROM publications AS p, associations AS a
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
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
            return new ResourceNotFound('publication', ['pmid' => $pmid]);
        }

        if ($publication['populated']) {
            return new DomainConflict('Metadata of publication with pmid %s are already populated.', $pmid);
        }

        // download the metadata.
        $result = $this->efetch->metadata($pmid);

        // return error.
        if (! $result['success']) {
            return new RuntimeFailure('Efetch error for pmid %s: %s', $pmid, $result['error']);
        }

        // update publication.
        $this->pdo->beginTransaction();

        $update_publication_sth->execute([$result['data'], $pmid]);

        $select_runs_sth->execute([$pmid]);

        while ($run = $select_runs_sth->fetch()) {
            $count_not_populated_sth->execute([$run['id']]);

            if (! $count_not_populated_sth->fetchColumn()) {
                $update_run_sth->execute([$run['id']]);
            }
        }

        $this->pdo->commit();

        // success !
        return new ResourceUpdated(new Publication($pmid));
    }
}
