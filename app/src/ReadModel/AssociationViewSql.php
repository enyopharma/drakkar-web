<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\Assertions\PublicationState;

final class AssociationViewSql implements AssociationViewInterface
{
    private \PDO $pdo;

    const SELECT_ASSOCIATION_SQL = <<<SQL
        SELECT a.run_id, p.pmid, a.state, a.annotation, p.metadata
        FROM associations AS a, publications AS p
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
        AND a.pmid = ?
    SQL;

    const SELECT_ASSOCIATIONS_SQL = <<<SQL
        SELECT a.run_id, p.pmid, a.state, a.annotation, p.metadata
        FROM associations AS a, publications AS p
        WHERE p.pmid = a.pmid
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

        return Statement::from($select_association_sth);
    }

    public function all(int $run_id, string $state, int $limit, int $offset): Statement
    {
        PublicationState::argument($state);

        $select_associations_sth = $this->pdo->prepare(self::SELECT_ASSOCIATIONS_SQL);

        $select_associations_sth->execute([$run_id, $state, $limit, $offset]);

        return Statement::from($select_associations_sth);
    }
}
