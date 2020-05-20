<?php

declare(strict_types=1);

namespace App\ReadModel;

interface AssociationViewInterface
{
    public function pmid(int $pmid): Statement;

    public function count(string $state): int;

    public function all(string $state, int $limit, int $offset): Statement;
}
