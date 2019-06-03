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

    $payload = json_decode($serialized, true);

    $query = strtoupper($payload['query']);

    $isoforms = [];

    foreach ($payload['subjects'] as $accession => $sequence) {
        $sequence = strtoupper($sequence);

        $cmd = implode(' ', ['/bin/alignment', $sequence, $query]);

        $process = new Symfony\Component\Process\Process($cmd);

        try {
            $process->mustRun();

            $output = $process->getOutput();

            $output = preg_replace('/[\r\n]+$/', '', trim($output));

            if (! empty($output)) {
                $lines = explode("\n", $output);

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
        }

        catch (Symfony\Component\Process\Exception\ProcessFailedException $e) {
            echo $e->getMessage();
        }
    }

    $client->publish('alignment', json_encode([
        'id' => $payload['id'],
        'alignment' => [
            'sequence' => $payload['query'],
            'isoforms' => $isoforms,
        ],
    ]));
}
