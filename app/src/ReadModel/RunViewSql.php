<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\Assertions\PublicationState;

final class RunViewSql implements RunViewInterface
{
    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs WHERE populated IS TRUE AND id = ?
    SQL;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT * FROM runs WHERE populated IS TRUE ORDER BY id DESC
    SQL;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT COUNT(*) FROM associations WHERE run_id = ? AND state = ?
    SQL;

    const EAGER_LOAD_COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) AS nb
        FROM associations
        GROUP BY run_id, state
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {}

    public function id(int $id, string ...$with): Statement
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);

        if ($select_run_sth === false) throw new \Exception;

        $select_run_sth->execute([$id]);

        if (!$run = $select_run_sth->fetch()) {
            return Statement::from([]);
        }

        if (in_array('nbs', $with)) {
            $run['nbs'][PublicationState::PENDING] = $this->nb($run['id'], PublicationState::PENDING);
            $run['nbs'][PublicationState::SELECTED] = $this->nb($run['id'], PublicationState::SELECTED);
            $run['nbs'][PublicationState::DISCARDED] = $this->nb($run['id'], PublicationState::DISCARDED);
            $run['nbs'][PublicationState::CURATED] = $this->nb($run['id'], PublicationState::CURATED);
        }

        return Statement::from([$run]);
    }

    public function all(string ...$with): Statement
    {
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);

        if ($select_runs_sth === false) throw new \Exception;

        $select_runs_sth->execute();

        $nbs = in_array('nbs', $with) ? $this->nbs() : [];

        return Statement::from($this->generator($select_runs_sth, $nbs));
    }

    private function nb(int $id, string $state): int
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        if ($count_publications_sth === false) throw new \Exception;

        $count_publications_sth->execute([$id, $state]);

        return $count_publications_sth->fetch(\PDO::FETCH_COLUMN) ?? 0;
    }

    private function nbs(): array
    {
        $count_publications_sth = $this->pdo->prepare(self::EAGER_LOAD_COUNT_PUBLICATIONS_SQL);

        if ($count_publications_sth === false) throw new \Exception;

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
                PublicationState::PENDING => $nbs[$row['id']][PublicationState::PENDING] ?? 0,
                PublicationState::SELECTED => $nbs[$row['id']][PublicationState::SELECTED] ?? 0,
                PublicationState::DISCARDED => $nbs[$row['id']][PublicationState::DISCARDED] ?? 0,
                PublicationState::CURATED => $nbs[$row['id']][PublicationState::CURATED] ?? 0,
            ]];
        }
    }
}
