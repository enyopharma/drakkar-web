<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class RunViewSql implements RunViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function selectRunsQuery(): Query
    {
        return Query::instance($this->pdo)
            ->select('id, type, name, created_at')
            ->from('runs')
            ->where('populated IS TRUE')
            ->where('deleted_at IS NULL');
    }

    public function id(int $id): Statement
    {
        $select_run_sth = $this->selectRunsQuery()
            ->where('id = ?')
            ->prepare();

        $select_run_sth->execute([$id]);

        return new Statement(
            $this->first($select_run_sth)
        );
    }

    public function all(): Statement
    {
        $select_runs_sth = $this->selectRunsQuery()
            ->orderby('created_at DESC, id DESC')
            ->prepare();

        $select_runs_sth->execute();

        return new Statement(
            $this->generator($select_runs_sth)
        );
    }

    private function first(\PDOStatement $sth): \Generator
    {
        if ($run = $sth->fetch()) {
            $count_publications_sth = Query::instance($this->pdo)
                ->select('run_id, state, COUNT(*) AS nb')
                ->from('associations')
                ->groupby('run_id, state')
                ->where('run_id = ?')
                ->prepare();

            $nbs = [];

            $count_publications_sth->execute([$run['id']]);

            while ($row = $count_publications_sth->fetch()) {
                $nbs[$row['run_id']][$row['state']] = $row['nb'];
            }

            yield $this->formatted($run, $nbs);
        }
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        $count_publications_sth = Query::instance($this->pdo)
            ->select('run_id, state, COUNT(*) AS nb')
            ->from('associations')
            ->groupby('run_id, state')
            ->prepare();

        $count_publications_sth->execute();

        $nbs = [];

        while ($row = $count_publications_sth->fetch()) {
            $nbs[$row['run_id']][$row['state']] = $row['nb'];
        }

        while ($run = $sth->fetch()) {
            yield $this->formatted($run, $nbs);
        }
    }

    private function formatted(array $run, array $nbs): array
    {
        return [
            'id' => $run['id'],
            'type' => $run['type'],
            'name' => $run['name'],
            'created_at' => $run['created_at'],
            'nbs' => [
                \Domain\Association::PENDING => $nbs[$run['id']][\Domain\Association::PENDING] ?? 0,
                \Domain\Association::SELECTED => $nbs[$run['id']][\Domain\Association::SELECTED] ?? 0,
                \Domain\Association::DISCARDED => $nbs[$run['id']][\Domain\Association::DISCARDED] ?? 0,
                \Domain\Association::CURATED => $nbs[$run['id']][\Domain\Association::CURATED] ?? 0,
            ],
        ];
    }
}
