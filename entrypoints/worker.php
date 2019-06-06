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

    $payload = json_decode($serialized, true);

    $isoforms = [];

    $query = strtoupper($payload['query']);

    foreach ($payload['sequences'] as $accession => $sequence) {
        $lines = [];

        $sequence = strtoupper($sequence);

        $cmd = implode(' ', ['/bin/alignment', $sequence, $query]);

        $process = new Symfony\Component\Process\Process($cmd);

        try {
            $process->mustRun();

            $output = $process->getOutput();

            $output = preg_replace('/[\r\n]+$/', '', trim($output));

            $lines = explode("\n", $output);
        }

        catch (Throwable $e) {
            //
        }

        $isoforms[] = [
            'accession' => $accession,
            'occurences' => array_map(function ($line) {
                list($start, $stop, $identity) = explode(';', $line);

                 return [
                     'start' => $start,
                     'stop' => $stop,
                     'identity' => $identity,
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
