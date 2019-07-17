<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

final class RunProjection implements ProjectionInterface
{
    const SELECT_RUN_SQL = <<<SQL
        SELECT *
        FROM runs
        WHERE populated = true
        AND deleted_at IS NULL
        AND id = ?
SQL;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT *
        FROM runs
        WHERE populated = true
        AND deleted_at IS NULL
        ORDER BY created_at DESC, id DESC
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

    public function rset(array $criteria = []): ResultSetInterface
    {
        return key_exists('id', $criteria)
            ? $this->id((int) $criteria['id'])
            : $this->all();
    }

    private function id(int $id): ResultSetInterface
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

            return new MappedResultSet(
                new ArrayResultSet($run),
                new RunMapper([$run['id'] => $nbs])
            );
        }

        return new EmptyResultSet(self::class, ['id' => $id]);
    }

    private function all(): ResultSetInterface
    {
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);
        $count_publications_sth = $this->pdo->prepare(self::EAGER_LOAD_COUNT_PUBLICATIONS_SQL);

        $select_runs_sth->execute();

        $nbs = [];

        $count_publications_sth->execute();

        while ($row = $count_publications_sth->fetch()) {
            $nbs[$row['run_id']][$row['state']] = $row['nb'];
        }

        return new MappedResultSet(
            new ArrayResultSet(...$select_runs_sth->fetchAll()),
            new RunMapper($nbs)
        );
    }
}
