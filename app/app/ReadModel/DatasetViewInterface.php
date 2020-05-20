<?php

declare(strict_types=1);

namespace App\ReadModel;

interface DatasetViewInterface
{
    public function all(string $type): Statement;
}
