<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

use App\Domain\Publication;

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

    public function id(int $id)
    {
        $select_run_sth = $this->selectRunsQuery()
            ->where('id = ?')
            ->prepare();

        $select_run_sth->execute([$id]);

        if ($run = $select_run_sth->fetch()) {
            $count_publications_sth = Query::instance($this->pdo)
                ->select('run_id, state, COUNT(*) AS nb')
                ->from('associations')
                ->groupby('run_id, state')
                ->where('run_id = ?')
                ->prepare();

            $nbs = [];

            $count_publications_sth->execute([$id]);

            while ($row = $count_publications_sth->fetch()) {
                $nbs[$row['run_id']][$row['state']] = $row['nb'];
            }

            return $this->formatted($run, $nbs);
        }

        return false;
    }

    public function all(): array
    {
        $select_runs_sth = $this->selectRunsQuery()
            ->orderby('created_at DESC, id DESC')
            ->prepare();

        $count_publications_sth = Query::instance($this->pdo)
            ->select('run_id, state, COUNT(*) AS nb')
            ->from('associations')
            ->groupby('run_id, state')
            ->prepare();

        $select_runs_sth->execute();
        $count_publications_sth->execute();

        $nbs = [];

        while ($row = $count_publications_sth->fetch()) {
            $nbs[$row['run_id']][$row['state']] = $row['nb'];
        }

        $runs = [];

        while ($run = $select_runs_sth->fetch()) {
            $runs[] = $this->formatted($run, $nbs);
        }

        return $runs;
    }

    private function formatted(array $run, array $nbs): array
    {
        return [
            'id' => $run['id'],
            'type' => $run['type'],
            'name' => $run['name'],
            'created_at' => $run['created_at'],
            'nbs' => [
                Publication::PENDING => $nbs[$run['id']][Publication::PENDING] ?? 0,
                Publication::SELECTED => $nbs[$run['id']][Publication::SELECTED] ?? 0,
                Publication::DISCARDED => $nbs[$run['id']][Publication::DISCARDED] ?? 0,
                Publication::CURATED => $nbs[$run['id']][Publication::CURATED] ?? 0,
            ],
        ];
    }
}
