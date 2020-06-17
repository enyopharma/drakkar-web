<?php

declare(strict_types=1);

namespace App\ReadModel;

interface RunViewInterface
{
    public function id(int $id, string ...$with): Statement;

    public function all(string ...$with): Statement;
}
