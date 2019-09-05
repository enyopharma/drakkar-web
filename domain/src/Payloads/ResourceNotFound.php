<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class ResourceNotFound implements DomainPayloadInterface
{
    private $name;

    private $id;

    public function __construct(string $name, array $id)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function message(): string
    {
        $ks = array_keys($this->id);
        $vs = array_values($this->id);

        return sprintf('No %s matching [%s]', $this->name, implode(', ', array_map(function ($k, $v) {
            return sprintf('\'%s\' => %s', $k, $v);
        }, $ks, $vs)));
    }

    public function data(): array
    {
        return [];
    }

    public function meta(): array
    {
        return [
            'message' => $this->message(),
        ];
    }
}
