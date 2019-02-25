<?php declare(strict_types=1);

namespace App\Repositories;

final class PublicationRepository
{
    private $pdo;

    const FROM_RUN = <<<SQL
SELECT p.*, a.run_id, a.state
FROM associations AS a, publications AS p
WHERE p.id = a.publication_id
AND a.run_id = ?
AND a.state = ?
LIMIT ? OFFSET ?
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fromRun(int $id, string $state, int $page = 1, int $limit = 10): ResultSet
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare(self::FROM_RUN);

        $stmt->execute([$id, $state, $limit, $offset]);

        return new ResultSet($stmt->fetchAll());
    }
}
