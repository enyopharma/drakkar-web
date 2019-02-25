<?php declare(strict_types=1);

namespace App\Repositories;

final class RunRepository
{
    private $pdo;

    const FIND = <<<SQL
SELECT r.*,
    COUNT(p.id) AS nb_pending,
    COUNT(s.id) AS nb_selected,
    COUNT(d.id) AS nb_discarded,
    COUNT(c.id) AS nb_curated
FROM runs AS r
LEFT JOIN associations AS p ON r.id = p.run_id AND p.state = ?
LEFT JOIN associations AS s ON r.id = s.run_id AND s.state = ?
LEFT JOIN associations AS d ON r.id = d.run_id AND d.state = ?
LEFT JOIN associations AS c ON r.id = c.run_id AND c.state = ?
WHERE r.deleted_at IS NULL AND r.id = ?
GROUP BY r.id
SQL;

    const ALL = <<<SQL
SELECT r.*,
    COUNT(p.id) AS nb_pending,
    COUNT(s.id) AS nb_selected,
    COUNT(d.id) AS nb_discarded,
    COUNT(c.id) AS nb_curated,
    COUNT(s.id) + COUNT(d.id) AS nb_precurated,
    COUNT(p.id) + COUNT(s.id) + COUNT(d.id) + COUNT(c.id) AS nb_total
FROM runs AS r
LEFT JOIN associations AS p ON r.id = p.run_id AND p.state = ?
LEFT JOIN associations AS s ON r.id = s.run_id AND s.state = ?
LEFT JOIN associations AS d ON r.id = d.run_id AND d.state = ?
LEFT JOIN associations AS c ON r.id = c.run_id AND c.state = ?
WHERE r.deleted_at IS NULL
GROUP BY r.id
ORDER BY r.created_at DESC, r.id DESC
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(int $id): array
    {
        $stmt = $this->pdo->prepare(self::FIND);

        $stmt->execute([
            Association::PENDING,
            Association::SELECTED,
            Association::DISCARDED,
            Association::CURATED,
            $id,
        ]);

        $runs = $stmt->fetchAll();

        if (count($runs) > 0) {
            return $runs[0];
        }

        throw new \RuntimeException(
            sprintf('No curation run with id %s', $id)
        );
    }

    public function all(): ResultSet
    {
        $stmt = $this->pdo->prepare(self::ALL);

        $stmt->execute([
            Association::PENDING,
            Association::SELECTED,
            Association::DISCARDED,
            Association::CURATED,
        ]);

        return new ResultSet($stmt->fetchAll());
    }
}
