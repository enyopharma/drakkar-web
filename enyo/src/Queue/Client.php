<?php declare(strict_types=1);

namespace Enyo\Queue;

final class Client
{
    private $client;

    private $priorities;

    public function __construct(\Predis\Client $client, string ...$priorities)
    {
        $this->client = $client;
        $this->priorities = $priorities;
    }

    public function enqueue(Job $job, string $priority = 'default'): void
    {
        if (in_array($priority, $this->priorities)) {
            $this->client->rpush($priority, json_encode($job));
        } else {
            throw new \LogicException(
                sprintf('No priority named \'%s\'', $priority)
            );
        }
    }
}
