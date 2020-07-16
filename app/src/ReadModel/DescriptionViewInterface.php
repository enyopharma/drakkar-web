<?php

declare(strict_types=1);

namespace App\ReadModel;

interface DescriptionViewInterface
{
    public function count(int $run_id, int $pmid): int;

    public function id(int $run_id, int $pmid, int $id): Statement;

    public function search(string $stable_id): Statement;

    public function all(int $run_id, int $pmid, int $limit, int $offset): Statement;
}
