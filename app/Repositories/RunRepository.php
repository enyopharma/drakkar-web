<?php declare(strict_types=1);

namespace App\Repositories;

final class RunRepository
{
    private $pdo;

    const FIND = <<<SQL
        SELECT * FROM runs WHERE id = ?
SQL;

    const COUNT_PUBLICATIONS = <<<SQL
        SELECT a.run_id, COUNT(p.id)
        FROM associations AS a, publications AS p
        WHERE p.id = a.publication_id AND a.run_id = ? AND a.state = ?
        GROUP BY a.run_id
SQL;

    const ALL = <<<SQL
        SELECT *
        FROM runs
        WHERE deleted_at IS NULL
        GROUP BY id
        ORDER BY created_at DESC, id DESC
SQL;

    const ALL_COUNT_PUBLICATIONS = <<<SQL
        SELECT a.run_id, COUNT(p.id)
        FROM associations AS a, publications AS p
        WHERE p.id = a.publication_id AND a.state = ?
        GROUP BY a.run_id
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(int $id): array
    {
        $run_stmt = $this->pdo->prepare(self::FIND);

        $run_stmt->execute([$id]);

        if ($run = $run_stmt->fetch()) {
            $count_stmt = $this->pdo->prepare(self::COUNT_PUBLICATIONS);

            foreach (Publication::STATES as $state) {
                $count_stmt->execute([$run['id'], $state]);

                $run['nbs'][$state] = $count_stmt->fetchColumn(1);
            }

            return $run;
        }

        throw new \RuntimeException(
            sprintf('No curation run with id %s', $id)
        );
    }

    public function all(): ResultSetInterface
    {
        $runs_stmt = $this->pdo->prepare(self::ALL);
        $count_stmt = $this->pdo->prepare(self::ALL_COUNT_PUBLICATIONS);

        $runs_stmt->execute();

        foreach (Publication::STATES as $state) {
            $count_stmt->execute([$state]);

            $nbs[$state] = $count_stmt->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
        }

        return new ResultSet(new RunCollection($runs_stmt, $nbs));
    }
}
