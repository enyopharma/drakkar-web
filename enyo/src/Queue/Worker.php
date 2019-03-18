<?php declare(strict_types=1);

namespace Enyo\Queue;

use Psr\Log\LoggerInterface;

final class Worker
{
    private $client;

    private $logger;

    private $map;

    private $priorities;

    public function __construct(
        \Predis\Client $client,
        LoggerInterface $logger,
        JobHandlerMap $map,
        string ...$priorities
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->map = $map;
        $this->priorities = $priorities;
    }

    public function run(): void
    {
        while (true) {
            $payload = $this->client->blpop(
                ...array_merge($this->priorities, [10])
            );

            if (! is_null($payload)) {
                [$name, $input] = json_decode($payload[1], true);

                $handler = $this->map->handler($name);

                $result = $handler($input);

                $context = [
                    'name' => $name,
                    'priority' => $payload[0],
                    'input' => $input,
                    'handler' => get_class($handler),
                ];

                $result->success()
                    ? $this->logger->info('Job successfully handled', $context)
                    : $this->logger->error($result->message(), $context);
            }
        }
    }
}
