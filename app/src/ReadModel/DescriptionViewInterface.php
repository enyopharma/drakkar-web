<?php

declare(strict_types=1);

namespace App\ReadModel;

interface DescriptionViewInterface
{
    public function search(string $stable_id): Statement;

    public function count(int $run_id, int $pmid, string $stable_id): int;

    public function all(int $run_id, int $pmid, string $stable_id, int $limit, int $offset): Statement;
}
