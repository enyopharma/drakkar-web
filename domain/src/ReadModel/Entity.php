<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class Entity implements EntityInterface
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data();
    }
}
