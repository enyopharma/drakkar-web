<?php

declare(strict_types=1);

namespace App\ReadModel;

interface PsimiInterface
{
    public function method(string $psimi_id): Result;

    public function methods(string $q, int $limit): ResultSet;
}
