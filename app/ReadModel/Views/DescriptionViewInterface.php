<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface DescriptionViewInterface
{
    public function count(int $run_id, int $pmid): int;

    /**
     * @return array|false
     */
    public function id(int $run_id, int $pmid, int $id);

    public function all(int $run_id, int $pmid, int $limit, int $offset): array;
}
