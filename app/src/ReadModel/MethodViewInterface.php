<?php

declare(strict_types=1);

namespace App\ReadModel;

interface MethodViewInterface
{
    public function id(int $id): Statement;

    public function search(string $query, int $limit): Statement;
}
