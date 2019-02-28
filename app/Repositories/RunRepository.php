<?php declare(strict_types=1);

namespace App\Repositories;

use Enyo\Sql\StatementMap;

final class RunRepository
{
    private $stmts;

    public function __construct(StatementMap $stmts)
    {
        $this->stmts = $stmts;
    }

    public function find(int $id): array
    {
        $stmts['run'] = $this->stmts->executed('runs/find', [$id]);

        if ($run = $stmts['run']->fetch()) {
            foreach (Publication::STATES as $state) {
                $stmts['count'] = $this->stmts->executed('publications/count.from_run', [
                    $run['id'],
                    $state,
                ]);

                $run['nbs'][$state] = ($nb = $stmts['count']->fetchColumn(1)) ? $nb : 0;
            }

            return $run;
        }

        throw new \RuntimeException(
            sprintf('No curation run with id %s', $id)
        );
    }

    public function all(): ResultSet
    {
        $stmts['runs'] = $this->stmts->executed('runs/select');

        foreach (Publication::STATES as $state) {
            $nbs[$state] = $this->stmts
                ->executed('runs/count.publications', [$state])
                ->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
        }

        return new ResultSet(new RunCollection($stmts['runs'], $nbs));
    }
}
