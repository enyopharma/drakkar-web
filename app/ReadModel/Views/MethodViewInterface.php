<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface MethodViewInterface
{
    /**
     * @return array|false
     */
    public function psimiId(string $psimi_id);

    public function search(string $q, int $limit): array;
}
