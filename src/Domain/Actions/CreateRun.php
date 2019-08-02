<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Run;
use Domain\Payloads\InputNotValid;
use Domain\Payloads\DomainConflict;
use Domain\Payloads\ResourceCreated;
use Domain\Payloads\DomainPayloadInterface;

final class CreateRun
{
    const INSERT_RUN_SQL = <<<SQL
        INSERT INTO runs (type, name) VALUES (?, ?)
SQL;

    const INSERT_PUBLICATION_SQL = <<<SQL
        INSERT INTO publications (pmid) VALUES (?)
SQL;

    const INSERT_ASSOCIATION_SQL = <<<SQL
        INSERT INTO associations (run_id, pmid) VALUES (?, ?)
SQL;

    const SELECT_PUBLICATION_SQL = <<<SQL
        SELECT * FROM publications WHERE pmid = ?
SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.type AS run_type, r.name AS run_name, a.pmid
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND r.type = ?
        AND a.pmid IN(%s)
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(string $type, string $name, int ...$pmids): DomainPayloadInterface
    {
        if (! in_array($type, Run::TYPES)) {
            return new InputNotValid([
                sprintf('Value \'%s\' is not a valid curation run type.', $type)
            ]);
        }

        $insert_run_sth = $this->pdo->prepare(self::INSERT_RUN_SQL);
        $insert_publication_sth = $this->pdo->prepare(self::INSERT_PUBLICATION_SQL);
        $insert_association_sth = $this->pdo->prepare(self::INSERT_ASSOCIATION_SQL);
        $select_publication_sth = $this->pdo->prepare(self::SELECT_PUBLICATION_SQL);
        $select_publications_sth = $this->pdo->prepare(
            vsprintf(self::SELECT_PUBLICATIONS_SQL, [
                implode(', ', array_pad([], count($pmids), '?')),
            ])
        );

        // return an error when any publication is already associated with a
        // publication curation run of the same type.
        $select_publications_sth->execute(array_merge([$type], $pmids));

        if ($publication = $select_publications_sth->fetch()) {
            return new DomainConflict('Publication with PMID %s is already associated with %s curation run %s (\'%s\')', ...[
                $publication['pmid'],
                $publication['run_type'],
                $publication['run_id'],
                $publication['run_name'],
            ]);
        }

        // insert the curation run, the missing pmids and associations.
        $this->pdo->beginTransaction();

        $insert_run_sth->execute([$type, $name]);

        $run['id'] = (int) $this->pdo->lastInsertId();

        foreach ($pmids as $pmid) {
            $select_publication_sth->execute([$pmid]);

            if (! $select_publication_sth->fetch()) {
                $insert_publication_sth->execute([$pmid]);
            }

            $insert_association_sth->execute([$run['id'], $pmid]);
        }

        $this->pdo->commit();

        // success !
        return new ResourceCreated(new Run($run['id']));
    }
}
