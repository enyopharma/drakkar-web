<?php

declare(strict_types=1);

/**
 * Set up the autoloader.
 */
require __DIR__ . '/../../vendor/autoload.php';

/**
 * Complete the env with local values.
 */
(new Symfony\Component\Dotenv\Dotenv(false))->load(__DIR__ . '/../../.env');

/**
 * Build the container.
 */
$files = array_merge(
    glob(__DIR__ . '/../../infrastructure/factories/*.php'),
    glob(__DIR__ . '/../../domain/factories/*.php'),
    glob(__DIR__ . '/factories/*.php')
);

$container = new Quanta\Container(array_reduce($files, function ($factories, $file) {
    return array_merge($factories, require $file);
}, []));

/**
 * Get the cli application.
 */
$application = (require __DIR__ . '/config/application.php')($container);

/**
 * Get the commands.
 */
$commands = (require __DIR__ . '/config/commands.php')($container);

/**
 * add the commands to the application.
 */
array_map([$application, 'add'], $commands);

/**
 * Run the cli application.
 */
return $application->run();
