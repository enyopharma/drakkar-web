<?php

declare(strict_types=1);

namespace App\ReadModel;

interface AssociationViewInterface
{
    public function pmid(int $run_id, int $pmid): Statement;

    public function all(int $run_id, string $state, int $limit, int $offset): Statement;
}
