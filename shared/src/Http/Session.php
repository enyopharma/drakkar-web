<?php declare(strict_types=1);

namespace Shared\Http;

use Psr\Http\Message\ServerRequestInterface;

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

    public function data(ServerRequestInterface $request): array
    {
        return [
            'previous' => (string) $request->getUri(),
        ];
    }
}
