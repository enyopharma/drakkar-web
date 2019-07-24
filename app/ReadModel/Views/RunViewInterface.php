<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface RunViewInterface
{
    /**
     * @return array|false
     */
    public function id(int $id);

    public function all(): array;
}
