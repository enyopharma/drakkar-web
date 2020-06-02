<?php

declare(strict_types=1);

namespace App\ReadModel;

final class AssociationViewSql implements AssociationViewInterface
{
    private \PDO $pdo;

    const SELECT_ASSOCIATION_SQL = <<<SQL
        SELECT r.id AS run_id, r.name AS run_name, r.type,
            p.pmid, a.state, a.annotation, p.metadata
        FROM runs AS r, associations AS a, publications AS p
        WHERE r.id = a.run_id
        AND p.pmid = a.pmid
        AND a.run_id = ?
        AND a.pmid = ?
    SQL;

    const SELECT_ASSOCIATIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.name AS run_name, r.type,
            p.pmid, a.state, a.annotation, p.metadata
        FROM runs AS r, associations AS a, publications AS p
        WHERE r.id = a.run_id
        AND p.pmid = a.pmid
        AND a.run_id = ?
        AND a.state = ?
        ORDER BY a.updated_at ASC, a.id ASC
        LIMIT ? OFFSET ?
    SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function pmid(int $run_id, int $pmid): Statement
    {
        $select_association_sth = $this->pdo->prepare(self::SELECT_ASSOCIATION_SQL);

        $select_association_sth->execute([$run_id, $pmid]);

        return Statement::from($this->generator($select_association_sth));
    }

    public function all(int $run_id, string $state, int $limit, int $offset): Statement
    {
        $select_associations_sth = $this->pdo->prepare(self::SELECT_ASSOCIATIONS_SQL);

        $select_associations_sth->execute([$run_id, $state, $limit, $offset]);

        return Statement::from($this->generator($select_associations_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield $row + ['run' => [
                'id' => $row['run_id'],
                'type' => $row['type'],
                'name' => $row['run_name'],
            ]];
        }
    }
}
