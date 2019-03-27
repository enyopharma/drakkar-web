<?php declare(strict_types=1);

namespace App\Domain;

final class InsertRun
{
    const INVALID_TYPE = 0;
    const NOT_UNIQUE = 1;

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
        SELECT r.name AS run_name, a.pmid
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
            return new DomainPayload(self::INVALID_TYPE);
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
            return new DomainPayload(self::NOT_UNIQUE, [
                'run' => ['name' => $publication['run_name']],
                'publication' => ['pmid' => $publication['pmid']],
            ]);
        }

        // insert the curation run, the missing pmids and associations.
        $this->pdo->beginTransaction();

        $insert_run_sth->execute([$type, $name]);

        $run['id'] = $this->pdo->lastInsertId();

        foreach ($pmids as $pmid) {
            $select_publication_sth->execute([$pmid]);

            if (! $select_publication_sth->fetch()) {
                $insert_publication_sth->execute([$pmid]);
            }

            $insert_association_sth->execute([$run['id'], $pmid]);
        }

        $this->pdo->commit();

        // success !
        return new DomainSuccess(['run' => $run]);
    }
}
