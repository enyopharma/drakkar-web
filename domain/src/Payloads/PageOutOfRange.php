<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class PageOutOfRange implements DomainPayloadInterface
{
    private $page;

    private $limit;

    public function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function data(): array
    {
        return [];
    }

    public function meta(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }
}
