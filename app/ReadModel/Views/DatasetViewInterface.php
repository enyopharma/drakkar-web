<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface DatasetViewInterface
{
    public function all(): \Generator;
}
