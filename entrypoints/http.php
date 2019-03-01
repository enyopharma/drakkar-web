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
 * Register slashtrace as error handler.
 */
require $root . '/config/slashtrace.php';

/**
 * Build the app container.
 */
$config = (require $root . '/config/app.php')($root);
$factories = (require $root . '/config/factories.php')($config);
$container = (require $root . '/config/container.php')($factories);

/**
 * Call boot scripts with the container.
 */
(require $root . '/config/session.php')($container);

/**
 * Run the http application.
 */
$container->get(Quanta\HttpEntrypoint::class)->run();
