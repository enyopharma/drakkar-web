<?php declare(strict_types=1);

namespace App\Repositories;

final class PublicationRepository
{
    private $pdo;

    const FROM_RUN = <<<SQL
SELECT p.*, a.run_id, a.state, a.annotation
FROM associations AS a, publications AS p
WHERE p.id = a.publication_id
AND a.run_id = ?
AND a.state = ?
ORDER BY id ASC
LIMIT ? OFFSET ?
SQL;

    const UPDATE = <<<SQL
UPDATE associations
SET state = ?, annotation = ?
WHERE run_id = ?
AND publication_id = ?
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fromRun(int $id, string $state, int $page = 1, int $limit = 10): ResultSetInterface
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare(self::FROM_RUN);

        $stmt->execute([$id, $state, $limit, $offset]);

        return new Pagination(new ResultSet($stmt->fetchAll()), 10, $page, $limit);
    }

    public function update(int $run_id, int $publication_id, string $state, string $annotation): bool
    {
        if (in_array($state, Publication::STATES)) {
            $stmt = $this->pdo->prepare(self::UPDATE);

            return $stmt->execute([$state, $annotation, $run_id, $publication_id]);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s is not a valid curation state (%s)', [
                $state, implode(', ', Publication::STATES),
            ])
        );
    }
}
