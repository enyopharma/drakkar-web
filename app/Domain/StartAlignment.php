<?php declare(strict_types=1);

namespace App\Domain;

use Ramsey\Uuid\Uuid;

final class StartAlignment
{
    private $client;

    public function __construct(\Predis\Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(string $query, array $subjects)
    {
        $id = Uuid::uuid4()->toString();

        $this->client->rpush('default', json_encode([
            'id' => $id,
            'query' => $query,
            'subjects' => $subjects,
        ]));

        return new DomainSuccess(['id' => $id]);
    }
}
