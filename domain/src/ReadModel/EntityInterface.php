<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface EntityInterface
{
    public function data(): array;
}
