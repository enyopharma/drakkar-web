<?php declare(strict_types=1);

namespace Shared\Http;

final class Session
{
    private $previous;

    public function populate(array $data): void
    {
        $this->previous = $data['previous'] ?? '';
    }

    public function previous(): string
    {
        return $this->previous;
    }

    public function data(): array
    {
        return [];
    }
}
