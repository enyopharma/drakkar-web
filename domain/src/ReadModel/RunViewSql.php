<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class RunViewSql implements RunViewInterface
{
    private $pdo;

    const SELECT_RUN_SQL = <<<SQL
        SELECT id, type, name, created_at
        FROM runs
        WHERE populated IS TRUE
        AND id = ?
SQL;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT id, type, name, created_at
        FROM runs
        WHERE populated IS TRUE
SQL;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) AS nb
        FROM associations
        GROUP BY run_id, state
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function id(int $id): Statement
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);

        $select_run_sth->execute([$id]);

        return Statement::from($this->generator($select_run_sth));
    }

    public function all(): Statement
    {
        $nbs = $this->eagerLoadedNbPublications();

        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);

        $select_runs_sth->execute();

        return Statement::from($this->generator($select_runs_sth, $nbs));
    }

    private function eagerLoadedNbPublications(): array
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $count_publications_sth->execute();

        $nbs = [];

        while ($row = $count_publications_sth->fetch()) {
            $nbs[$row['run_id']][$row['state']] = $row['nb'];
        }

        return $nbs;
    }

    private function generator(\PDOStatement $sth, array $nbs = []): \Generator
    {
        while ($row = $sth->fetch()) {
            $nbs = [
                \Domain\Publication::PENDING => $nbs[$row['id']][\Domain\Publication::PENDING] ?? 0,
                \Domain\Publication::SELECTED => $nbs[$row['id']][\Domain\Publication::SELECTED] ?? 0,
                \Domain\Publication::DISCARDED => $nbs[$row['id']][\Domain\Publication::DISCARDED] ?? 0,
                \Domain\Publication::CURATED => $nbs[$row['id']][\Domain\Publication::CURATED] ?? 0,
            ];

            yield new RunSql(
                $this->pdo,
                $row['id'],
                $row['type'],
                $row['name'],
                $row + ['nbs' => $nbs]
            );
        }
    }
}
