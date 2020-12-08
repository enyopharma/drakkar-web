<?php

declare(strict_types=1);

namespace App\ReadModel;

final class PublicationViewSql implements PublicationViewInterface
{
    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.type AS run_type, r.name AS run_name, p.pmid, a.state, a.annotation, p.metadata
        FROM runs AS r, associations AS a, publications AS p
        WHERE r.id = a.run_id
        AND p.pmid = a.pmid
        AND p.pmid = ?
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {}

    public function search(int $pmid): Statement
    {
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);

        if ($select_publications_sth === false) throw new \Exception;

        $select_publications_sth->execute([$pmid]);

        return Statement::from($this->generator($select_publications_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield $row + ['run' => [
                'id' => $row['run_id'],
                'type' => $row['run_type'],
                'name' => $row['run_name'],
            ]];
        }
    }
}
