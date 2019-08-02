<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\AlignmentStarted;
use Domain\Payloads\DomainPayloadInterface;

final class StartAlignment
{
    private $client;

    public function __construct(\Predis\Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(string $id, string $query, array $sequences): DomainPayloadInterface
    {
        $this->client->rpush('default', json_encode([
            'id' => $id,
            'query' => $query,
            'sequences' => $sequences,
        ]));

        return new AlignmentStarted;
    }
}
