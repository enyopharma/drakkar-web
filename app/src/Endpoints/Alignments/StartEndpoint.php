<?php

declare(strict_types=1);

namespace App\Endpoints\Alignments;

use Predis\Client;

#[\App\Attributes\Method('POST')]
#[\App\Attributes\Pattern('/jobs/alignments')]
final class StartEndpoint
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function __invoke(callable $input): array
    {
        $data = [
            'id' => $input('id'),
            'query' => $input('query'),
            'sequences' => array_filter((array) $input('sequences', []), 'is_string'),
        ];

        $payload = json_encode($data, JSON_THROW_ON_ERROR);

        $this->client->rpush('default', [$payload]);

        return [];
    }
}
