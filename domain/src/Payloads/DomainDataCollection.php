<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class DomainDataCollection implements DomainPayloadInterface
{
    private $data;

    private $meta;

    public function __construct(array $data, array $meta = [])
    {
        $this->data = $data;
        $this->meta = $meta;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function meta(): array
    {
        return $this->meta;
    }
}
