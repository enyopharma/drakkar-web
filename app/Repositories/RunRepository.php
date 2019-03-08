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

    public function insert(string $type, string $name, int ...$pmids): int
    {
        // ensure type is valid.
        if (! in_array($type, Run::TYPES)) {
            throw new \UnexpectedValueException(
                vsprintf('%s is not a valid curation run type (%s)', [
                    $type, implode(', ', Publication::STATES),
                ])
            );
        }

        // ensure no pmid is already associated to a run with the given type.
        $stmts['pmids'] = $this->stmts->executed('publications/select.type.pmids', ...[
            array_merge([$type], $pmids),
            count($pmids),
        ]);

        if ($row = $stmts['pmids']->fetch()) {
            throw new NotUniqueException(
                vsprintf('PMID %s is already associated with %s curation run with id %s (\'%s\')', [
                    $row['pmid'],
                    $type,
                    $row['run_id'],
                    $row['run_name'],
                ])
            );
        }

        // insert the curation run and the associated publications.
        return $this->stmts->transaction(function ($stmts) use ($type, $name, $pmids) {
            $run['id'] = $stmts->inserted('runs/insert', [$type, $name]);

            foreach ($pmids as $pmid) {
                $stmt = $stmts->executed('publications/find.pmid', [$pmid]);

                if (! $publication = $stmt->fetch()) {
                    $publication['id'] = $stmts->inserted('publications/insert', [$pmid]);
                }

                $stmts->executed('runs.publications/insert', [
                    $run['id'],
                    $publication['id'],
                ]);
            }

            return $run['id'];
        });
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
