<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Process\Process;

/**
 * Set up the autoloader.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Complete the env with local values.
 */
if (file_exists($envfile = __DIR__ . '/../.env')) {
    (new Dotenv(false))->load($envfile);
}

/**
 * Get the predis client.
 */
$client = new Predis\Client([
    'scheme' => $_ENV['REDIS_SCHEME'] ?? 'tcp',
    'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
    'port' => $_ENV['REDIS_PORT'] ?? '6379',
]);

/**
 * Run the queue listener.
 */
while (true) {
    $entry = $client->blpop('default', 10);

    if (is_null($entry)) continue;

    [$queue, $serialized] = $entry;

    $payload = json_decode($serialized, true);

    $isoforms = [];

    $query = strtoupper($payload['query']);

    foreach ($payload['sequences'] as $accession => $sequence) {
        $lines = [];

        $sequence = strtoupper($sequence);

        $process = new Symfony\Component\Process\Process(['/bin/alignment', $sequence, $query]);

        try {
            $process->mustRun();

            $output = $process->getOutput();

            $output = (string) preg_replace('/[\r\n]+$/', '', trim($output));

            $lines = explode("\n", $output);
        }

        catch (Throwable $e) {
            echo $e;
        }

        $isoforms[] = [
            'accession' => $accession,
            'occurrences' => array_map(function ($line) {
                list($start, $stop, $identity) = explode(';', $line);

                 return [
                     'start' => (int) $start,
                     'stop' => (int) $stop,
                     'identity' => round((float) $identity, 2),
                 ];
            }, $lines),
        ];
    }

    $client->publish('alignment', json_encode([
        'id' => $payload['id'],
        'alignment' => [
            'sequence' => $query,
            'isoforms' => $isoforms,
        ],
    ]));
}
