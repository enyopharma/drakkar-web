<?php

declare(strict_types=1);

/**
 * Get the root path.
 */
$root = (string) realpath(__DIR__ . '/../');

/**
 * Setup the autoloader.
 */
require $root . '/vendor/autoload.php';

/**
 * Get the app config.
 */
$config = (require $root . '/config/app.php')($root, 'worker', true);

/**
 * Get the app container.
 */
$container = (require $root . '/container.php')($config);

/**
 * Read the redis queue.
 */
$client = $container->get(Predis\Client::class);

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
            //
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
