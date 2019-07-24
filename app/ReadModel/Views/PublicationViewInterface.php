<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface PublicationViewInterface
{
    public function count(int $run_id, string $state): int;

    /**
     * @return array|false
     */
    public function pmid(int $run_id, int $pmid);

    public function all(int $run_id, string $state, int $limit, int $offset): array;
}
