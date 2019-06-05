<?php declare(strict_types=1);

namespace App\Domain;

final class StartAlignment
{
    private $client;

    public function __construct(\Predis\Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(string $id, string $query, array $sequences)
    {
        $this->client->rpush('default', json_encode([
            'id' => $id,
            'query' => $query,
            'sequences' => $sequences,
        ]));

        return new DomainSuccess;
    }
}
