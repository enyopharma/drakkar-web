<?php

declare(strict_types=1);

namespace App\ReadModel;

final class RunViewSql implements RunViewInterface
{
    private \PDO $pdo;

    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs WHERE populated IS TRUE AND id = ?
    SQL;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT * FROM runs WHERE populated IS TRUE
    SQL;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT COUNT(*) FROM associations WHERE run_id = ? AND state = ?
    SQL;

    const EAGER_LOAD_COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) AS nb
        FROM associations
        GROUP BY run_id, state
    SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function id(int $id, string ...$with): Statement
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);

        $select_run_sth->execute([$id]);

        if (!$run = $select_run_sth->fetch()) {
            return Statement::from([]);
        }

        if (in_array('nbs', $with)) {
            $run['nbs']['pending'] = $this->nb($run['id'], 'pending');
            $run['nbs']['selected'] = $this->nb($run['id'], 'selected');
            $run['nbs']['discarded'] = $this->nb($run['id'], 'discarded');
            $run['nbs']['curated'] = $this->nb($run['id'], 'curated');
        }

        return Statement::from([$run]);
    }

    public function all(string ...$with): Statement
    {
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);

        $select_runs_sth->execute();

        $nbs = in_array('nbs', $with) ? $this->nbs() : [];

        return Statement::from($this->generator($select_runs_sth, $nbs));
    }

    private function nb(int $id, string $state): int
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $count_publications_sth->execute([$id, $state]);

        return $count_publications_sth->fetch(\PDO::FETCH_COLUMN) ?? 0;
    }

    private function nbs(): array
    {
        $count_publications_sth = $this->pdo->prepare(self::EAGER_LOAD_COUNT_PUBLICATIONS_SQL);

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
            yield $row + ['nbs' => [
                'pending' => $nbs[$row['id']]['pending'] ?? 0,
                'selected' => $nbs[$row['id']]['selected'] ?? 0,
                'discarded' => $nbs[$row['id']]['discarded'] ?? 0,
                'curated' => $nbs[$row['id']]['curated'] ?? 0,
            ]];
        }
    }
}
