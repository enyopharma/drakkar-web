<?php declare(strict_types=1);

namespace App\Repositories;

use Enyo\Data\ResultSet;
use Enyo\Data\Pagination;
use Enyo\Data\StatementMap;

final class PublicationRepository
{
    private $stmts;

    public function __construct(StatementMap $stmts)
    {
        $this->stmts = $stmts;
    }

    public function paginated(int $run_id, string $state, int $page = 1, int $limit = 20): Pagination
    {
        $offset = ($page - 1) * $limit;

        $stmts['select'] = $this->stmts->executed('runs.publications/select', [
            $run_id,
            $state,
            $limit,
            $offset,
        ]);

        $stmts['count'] = $this->stmts->executed('runs.publications/count', [
            $run_id,
            $state,
        ]);

        $publications = $stmts['select']->fetchAll();
        $total = ($nb = $stmts['count']->fetchColumn(1)) ? $nb : 0;

        return new Pagination(new ResultSet($publications), $total, $page, $limit);
    }

    public function update(int $run_id, int $publication_id, string $state, string $annotation): \PDOStatement
    {
        if (in_array($state, Publication::STATES)) {
            return $this->stmts->executed('runs.publications/update', [
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
