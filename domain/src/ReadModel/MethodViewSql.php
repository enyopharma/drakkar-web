<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class MethodViewSql implements MethodViewInterface
{
    private $pdo;

    const SELECT_METHOD_SQL = <<<SQL
        SELECT id, psimi_id, name
        FROM methods
        WHERE psimi_id = ?
SQL;

    const SELECT_METHODS_SQL = <<<SQL
        SELECT id, psimi_id, name
        FROM methods
        WHERE %s
        LIMIT ?
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function psimiId(string $psimi_id): Statement
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$psimi_id]);

        return Statement::from($this->generator($select_method_sth));
    }

    public function search(string $query, int $limit): Statement
    {
        $qs = array_map(function ($q) {
            return '%' . trim($q) . '%';
        }, array_filter(explode('+', $query)));

        if (count($qs) == 0) {
            return Statement::from([]);
        }

        $where = implode(' AND ', array_pad([], count($qs), 'search ILIKE ?'));

        $select_methods_sth = $this->pdo->prepare(sprintf(self::SELECT_METHODS_SQL, $where));

        $select_methods_sth->execute([...$qs, $limit]);

        return Statement::from($this->generator($select_methods_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield new MethodSql(
                $this->pdo,
                $row['id'],
                $row['psimi_id'],
                $row
            );
        }
    }
}
