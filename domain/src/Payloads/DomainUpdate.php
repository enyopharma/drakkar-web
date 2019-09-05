<?php

declare(strict_types=1);

namespace Domain\Payloads;

use Domain\ResourceInterface;

abstract class DomainUpdate implements DomainPayloadInterface
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function id(): array
    {
        return $this->resource->id();
    }

    public function idstr(): string
    {
        $ks = array_keys($this->resource->id());
        $vs = array_values($this->resource->id());

        return '[' . implode(', ', array_map(function ($k, $v) {
            return sprintf('\'%s\' => %s', $k, $v);
        }, $ks, $vs)) . ']';
    }

    public function isAbout(string $class): bool
    {
        return $this->resource instanceof $class;
    }

    public function data(): array
    {
        return $this->resource->id();
    }

    public function meta(): array
    {
        return [
            'resource' => get_class($this->resource),
        ];
    }
}
