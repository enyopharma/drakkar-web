<?php declare(strict_types=1);

namespace App\Repositories;

final class RunRepository
{
    private $pdo;

    const ALL = <<<SQL
SELECT r.*, COUNT(a.id) AS count_publications
FROM runs AS r
LEFT JOIN associations AS a ON r.id = a.run_id
WHERE r.deleted_at IS NULL
GROUP BY r.id
ORDER BY r.created_at DESC, r.id DESC
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): ResultSet
    {
        return new ResultSet($this->pdo->query(self::ALL));
    }
}
