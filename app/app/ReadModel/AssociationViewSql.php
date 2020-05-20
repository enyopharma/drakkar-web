<?php

declare(strict_types=1);

namespace App\ReadModel;

final class AssociationViewSql implements AssociationViewInterface
{
    private \PDO $pdo;

    private int $run_id;

    private array $data;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT COUNT(*)
        FROM associations
        WHERE run_id = ?
        AND state = ?
SQL;

    const SELECT_PUBLICATION_SQL = <<<SQL
        SELECT p.pmid, a.state, a.annotation, p.metadata
        FROM associations AS a, publications AS p
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
        AND a.pmid = ?
SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT p.pmid, a.state, a.annotation, p.metadata
        FROM associations AS a, publications AS p
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
        AND a.state = ?
        ORDER BY a.updated_at ASC, a.id ASC
        LIMIT ? OFFSET ?
SQL;

    public function __construct(\PDO $pdo, int $run_id, array $data = [])
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->data = $data;
    }

    public function count(string $state): int
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $count_publications_sth->execute([$this->run_id, $state]);

        return $count_publications_sth->fetch(\PDO::FETCH_COLUMN) ?? 0;
    }

    public function pmid(int $pmid): Statement
    {
        $select_publication_sth = $this->pdo->prepare(self::SELECT_PUBLICATION_SQL);

        $select_publication_sth->execute([$this->run_id, $pmid]);

        return Statement::from($this->generator($select_publication_sth));
    }

    public function all(string $state, int $limit, int $offset): Statement
    {
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);

        $select_publications_sth->execute([$this->run_id, $state, $limit, $offset]);

        return Statement::from($this->generator($select_publications_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield new PublicationSql(
                $this->pdo,
                $this->run_id,
                $row['pmid'],
                $row['state'],
                $row['metadata'],
                $row + $this->data
            );
        }
    }
}
