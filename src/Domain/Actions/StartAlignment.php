<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\AlignmentStarted;
use Domain\Payloads\DomainPayloadInterface;

final class StartAlignment implements DomainActionInterface
{
    private $client;

    public function __construct(\Predis\Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $id = (string) $input['id'];
        $query = (string) $input['query'];
        $sequences = array_filter((array) ($input['sequences'] ?? []), 'is_string');

        $this->client->rpush('default', json_encode([
            'id' => $id,
            'query' => $query,
            'sequences' => $sequences,
        ]));

        return new AlignmentStarted;
    }
}
