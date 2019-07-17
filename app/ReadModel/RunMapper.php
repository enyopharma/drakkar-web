<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

final class RunMapper
{
    private $nbs;

    public function __construct(array $nbs)
    {
        $this->nbs = $nbs;
    }

    public function __invoke(array $run): array
    {
        return $run+= ['nbs' => [
            Publication::PENDING => $this->nbs[$run['id']][Publication::PENDING] ?? 0,
            Publication::SELECTED => $this->nbs[$run['id']][Publication::SELECTED] ?? 0,
            Publication::DISCARDED => $this->nbs[$run['id']][Publication::DISCARDED] ?? 0,
            Publication::CURATED => $this->nbs[$run['id']][Publication::CURATED] ?? 0,
        ]];
    }
}
