<?php declare(strict_types=1);

namespace App\ReadModel;

final class MethodProjection implements ProjectionInterface
{
    const SELECT_METHOD_SQL = <<<SQL
        SELECT * FROM methods WHERE psimi_id = ?
SQL;

    const SEARCH_METHODS_SQL = <<<SQL
        SELECT psimi_id, name FROM methods WHERE %s LIMIT ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function rset(array $criteria = []): ResultSetInterface
    {
        return key_exists('psimi_id', $criteria)
            ? $this->psimi_id((string) ($criteria['psimi_id'] ?? ''))
            : $this->search(
                (string) ($criteria['q'] ?? ''),
                (int) ($criteria['limit'] ?? 20)
            );
    }

    private function psimi_id(string $psimi_id): ResultSetInterface
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$psimi_id]);

        return ($method = $select_method_sth->fetch())
            ? new ArrayResultSet($method)
            : new EmptyResultSet(self::class, ['psimi_id' => $psimi_id]);
    }

    private function search(string $q, int $limit): ResultSetInterface
    {
        $parts = (array) preg_split('/\s+/', $q);

        $select_methods_sth = $this->pdo->prepare(vsprintf(self::SEARCH_METHODS_SQL, [
            implode(' AND ', array_pad([], count($parts), 'search ILIKE ?')),
        ]));

        $select_methods_sth->execute(array_merge(array_map(function ($part) {
            return '%' . $part . '%';
        }, $parts), [$limit]));

        return new ArrayResultSet(...$select_methods_sth->fetchAll());
    }
}
