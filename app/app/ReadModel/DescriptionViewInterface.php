<?php

declare(strict_types=1);

namespace App\ReadModel;

interface DescriptionViewInterface
{
    public function count(): int;

    public function id(int $id): Statement;

    public function all(int $limit, int $offset): Statement;
}