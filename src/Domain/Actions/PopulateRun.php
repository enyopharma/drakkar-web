<?php

declare(strict_types=1);

namespace Domain\Actions;

use Services\Efetch;

use Domain\Run;
use Domain\Publication;
use Domain\Payloads\RuntimeFailure;
use Domain\Payloads\DomainConflict;
use Domain\Payloads\ResourceUpdated;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class PopulateRun implements DomainActionInterface
{
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

    public function __invoke(array $input, callable $listen = null): DomainPayloadInterface
    {
        $id = (int) $input['id'];

        // prepare the queries.
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);
        $update_run_sth = $this->pdo->prepare(self::UPDATE_RUN_SQL);
        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);

        // select the curation run.
        $select_run_sth->execute([$id]);

        if (! $run = $select_run_sth->fetch()) {
            return new ResourceNotFound('run', ['id' => $id]);
        }

        if ($run['populated']) {
            return new DomainConflict('Metadata of curation run %s publications are already populated', $id);
        }

        // select the non populated publications of the curation run.
        $errors = 0;

        $select_publications_sth->execute([$run['id']]);

        while ($publication = $select_publications_sth->fetch()) {
            $result = $this->efetch->metadata($publication['pmid']);

            if (! is_null($listen)) {
                $payload = $result['success']
                    ? new ResourceUpdated(new Publication($publication['pmid']))
                    : new RuntimeFailure('Efetch error for pmid %s: %s', $publication['pmid'], $result['error']);

                $listen($payload);
            }

            $result['success']
                ? $update_publication_sth->execute([$result['data'], $publication['pmid']])
                : $errors++;
        }

        // return a failure when there is errors.
        if ($errors > 0) {
            return new RuntimeFailure('Failed to update metadata of %s publications', $errors);
        }

        // update the curation run state when no error happened.
        $update_run_sth->execute([$run['id']]);

        // success !
        return new ResourceUpdated(new Run($run['id']));
    }
}
