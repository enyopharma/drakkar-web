<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Message\ServerRequestInterface;

final class Session
{
    private $previous;

    private $populated = false;

    public function populate(array $data): void
    {
        $this->previous = $data['previous'] ?? '';

        $this->populated = true;
    }

    public function previous(): string
    {
        if ($this->populated) {
            return $this->previous;
        }

        throw new \RuntimeException($this->notPopulatedErrorMessage());
    }

    public function data(ServerRequestInterface $request): array
    {
        return [
            'previous' => (string) $request->getUri(),
        ];
    }

    private function notPopulatedErrorMessage(): string
    {
        return 'The session has not been populated yet';
    }
}
