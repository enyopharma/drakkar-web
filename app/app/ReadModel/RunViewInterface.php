<?php

declare(strict_types=1);

namespace App\ReadModel;

interface RunViewInterface
{
    public function id(int $id): Statement;

    public function all(): Statement;
}
