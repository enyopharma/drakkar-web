<?php

declare(strict_types=1);

namespace App\Endpoints\Alignments;

use Psr\Http\Message\ServerRequestInterface;

use Predis\Client;

final class StartEndpoint
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = (array) $request->getParsedBody();

        $id = (string) $params['id'];
        $query = (string) $params['query'];
        $sequences = array_filter((array) ($params['sequences'] ?? []), 'is_string');

        $payload = json_encode(['id' => $id, 'query' => $query, 'sequences' => $sequences]);

        if ($payload === false) {
            throw new \Exception;
        }

        $this->client->rpush('default', [$payload]);

        return [];
    }
}
