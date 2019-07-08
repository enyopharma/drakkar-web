<?php declare(strict_types=1);

namespace App\ReadModel;

final class MethodProjection
{
    const SELECT_FROM_ID_SQL = <<<SQL
        SELECT * FROM methods WHERE id = ?
SQL;

    const SELECT_FROM_PSIMI_ID_SQL = <<<SQL
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

    public function id(int $id): array
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_FROM_ID_SQL);

        $select_method_sth->execute([$id]);

        if ($method = $select_method_sth->fetch()) {
            return $method;
        }

        throw new NotFoundException(
            sprintf('%s has no entry with id %s', self::class, $id)
        );
    }

    public function psimi_id(string $psimi_id): array
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_FROM_PSIMI_ID_SQL);

        $select_method_sth->execute([$psimi_id]);

        if ($method = $select_method_sth->fetch()) {
            return $method;
        }

        throw new NotFoundException(
            sprintf('%s has no entry with psimi_id \'%s\'', self::class, $psimi_id)
        );
    }

    public function search(string $q, int $limit = 20): ResultSetInterface
    {
        $parts = (array) preg_split('/\s+/', $q);

        $select_methods_sth = $this->pdo->prepare(vsprintf(self::SEARCH_METHODS_SQL, [
            implode(' AND ', array_pad([], count($parts), 'search ILIKE ?')),
        ]));

        $select_methods_sth->execute(array_merge(array_map(function ($part) {
            return '%' . $part . '%';
        }, $parts), [$limit]));

        return new ResultSet($select_methods_sth->fetchAll());
    }
}
