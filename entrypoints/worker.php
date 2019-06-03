<?php declare(strict_types=1);

/**
 * Get the root path.
 */
$root = (string) realpath(__DIR__ . '/../');

/**
 * Setup the autoloader.
 */
require $root . '/vendor/autoload.php';

/**
 * Load the env.
 */
(new Dotenv\Dotenv($root))->load();

/**
 * Build the app container.
 */
 $app = (require $root . '/config/app.php')($root);
 $factories = (require $root . '/config/factories.php')($app);
 $container = (require $root . '/config/container.php')($factories);

/**
 * Read the redis queue.
 */
$client = $container->get(Predis\Client::class);

while (true) {
    $entry = $client->blpop('default', 10);

    if (is_null($entry)) continue;

    [$queue, $serialized] = $entry;

    echo sprintf('%s - %s', $queue, $serialized) . PHP_EOL;

    sleep(1);

    $payload = json_decode($serialized, true);

    $isoforms = [];

    foreach ($payload['subjects'] as $accession => $sequence) {
        $isoforms[] = [
            'accession' => $accession,
            'occurences' => [],
        ];
    }

    $client->publish('alignment', json_encode([
        'id' => $payload['id'],
        'alignment' => [
            'sequence' => $payload['query'],
            'isoforms' => $isoforms,
        ],
    ]));
}
