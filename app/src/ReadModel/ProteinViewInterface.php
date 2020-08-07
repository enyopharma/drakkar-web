<?php

declare(strict_types=1);

namespace App\ReadModel;

interface ProteinViewInterface
{
    public function id(int $id, string ...$with): Statement;

    public function search(string $type, string $query, int $limit): Statement;
}
