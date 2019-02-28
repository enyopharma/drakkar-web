<?php declare(strict_types=1);

namespace App\Repositories;

use Shared\Sql\StatementMap;

final class PublicationRepository
{
    private $stmts;

    public function __construct(StatementMap $stmts)
    {
        $this->stmts = $stmts;
    }

    public function fromRun(int $run_id, string $state, int $page = 1, int $limit = 20): ResultSetInterface
    {
        $offset = ($page - 1) * $limit;

        $stmts['select'] = $this->stmts->executed('publications/select.from_run', [
            $run_id,
            $state,
            $limit,
            $offset,
        ]);

        $stmts['count'] = $this->stmts->executed('publications/count.from_run', [
            $run_id,
            $state,
        ]);

        $publications = $stmts['select']->fetchAll();
        $total = ($nb = $stmts['count']->fetchColumn()) ? $nb : 0;

        return new Pagination(new ResultSet($publications), $total, $page, $limit);
    }

    public function update(int $run_id, int $publication_id, string $state, string $annotation): \PDOStatement
    {
        if (in_array($state, Publication::STATES)) {
            return $this->stmts->executed('publications/update', [
                $state,
                $annotation,
                $run_id,
                $publication_id,
            ]);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s is not a valid curation state (%s)', [
                $state, implode(', ', Publication::STATES),
            ])
        );
    }
}
