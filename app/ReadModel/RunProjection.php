<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

final class RunProjection
{
    const SELECT_RUNS_SQL = <<<SQL
        SELECT * FROM runs
        WHERE populated = true
        AND deleted_at IS NULL
        ORDER BY created_at DESC, id DESC
SQL;

    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs
        WHERE populated = true
        AND deleted_at IS NULL
        AND id = ?
SQL;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) as nb
        FROM associations
        WHERE run_id = ? AND state = ?
        GROUP BY run_id, state
SQL;

    const EAGER_LOAD_COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) as nb
        FROM associations
        GROUP BY run_id, state
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function id(int $id): array
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $select_run_sth->execute([$id]);

        if ($run = $select_run_sth->fetch()) {
            $nbs = [];

            foreach (Publication::STATES as $state) {
                $count_publications_sth->execute([$run['id'], $state]);

                $nbs[$state] = ($nb = $count_publications_sth->fetchColumn(2)) ? $nb : 0;
            }

            return $this->formatted($run, $nbs);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with id %s', self::class, $id)
        );
    }

    public function all(): ResultSetInterface
    {
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);
        $count_publications_sth = $this->pdo->prepare(self::EAGER_LOAD_COUNT_PUBLICATIONS_SQL);

        $nbs = [];

        $count_publications_sth->execute();

        while ($row = $count_publications_sth->fetch()) {
            $nbs[$row['run_id']][$row['state']] = $row['nb'];
        }

        $runs = [];

        $select_runs_sth->execute();

        while ($run = $select_runs_sth->fetch()) {
            $runs[] = $this->formatted($run, $nbs[$run['id']]);
        }

        return new ResultSet($runs);
    }

    private function formatted(array $run, array $nbs): array
    {
        return $run+= ['nbs' => [
            Publication::PENDING => $nbs[Publication::PENDING] ?? 0,
            Publication::SELECTED => $nbs[Publication::SELECTED] ?? 0,
            Publication::DISCARDED => $nbs[Publication::DISCARDED] ?? 0,
            Publication::CURATED => $nbs[Publication::CURATED] ?? 0,
        ]];
    }
}
