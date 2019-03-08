<?php declare(strict_types=1);

namespace App\Repositories;

use Enyo\Data\ResultSet;
use Enyo\Data\StatementMap;

final class RunRepository
{
    private $stmts;

    public function __construct(StatementMap $stmts)
    {
        $this->stmts = $stmts;
    }

    public function find(int $id): array
    {
        $stmts['run'] = $this->stmts->executed('runs/find', [Run::POPULATED, $id]);

        if ($run = $stmts['run']->fetch()) {
            foreach (Publication::STATES as $state) {
                $stmts['count'] = $this->stmts->executed('runs.publications/count', [
                    $run['id'],
                    $state,
                ]);

                $run['nbs'][$state] = ($nb = $stmts['count']->fetchColumn(1)) ? $nb : 0;
            }

            return $run;
        }

        throw new NotFoundException(
            sprintf('No curation run with id %s', $id)
        );
    }

    public function all(): ResultSet
    {
        $runs = $this->stmts->executed('runs/select', [Run::POPULATED])->fetchAll();

        foreach (Publication::STATES as $state) {
            $nbs[$state] = $this->stmts
                ->executed('runs.publications/count.eagerload', [$state])
                ->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);
        }

        return new ResultSet(new RunCollection($runs, $nbs));
    }
}
