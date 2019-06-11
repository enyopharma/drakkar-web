<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\ResultSetInterface;

final class PrecurationProjection
{
    private $runs;

    private $publications;

    public function __construct(RunProjection $runs, PublicationProjection $publications)
    {
        $this->runs = $runs;
        $this->publications = $publications;
    }

    public function id(int $run_id, string $state, int $page = 1, int $limit = 20): array
    {
        $run = $this->runs->id($run_id);

        return $run + [
            'nbs' => [
                Publication::PENDING => $this->publications->count($run_id, Publication::PENDING),
                Publication::SELECTED => $this->publications->count($run_id, Publication::SELECTED),
                Publication::DISCARDED => $this->publications->count($run_id, Publication::DISCARDED),
                Publication::CURATED => $this->publications->count($run_id, Publication::CURATED),
            ],
            'publications' => $this->publications->pagination($run_id, $state, $page, $limit),
        ];
    }

    public function max(int $run_id, string $state, int $limit = 20): int
    {
        $total = $this->publications->count($run_id, $state);

        return (int) $total/$limit;
    }
}
