<?php

declare(strict_types=1);

namespace App\ReadModel;

final class Entity implements EntityInterface
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }
}
