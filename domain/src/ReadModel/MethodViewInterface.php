<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface MethodViewInterface
{
    public function psimiId(string $psimi_id): Statement;

    public function search(string $query, int $limit): Statement;
}
