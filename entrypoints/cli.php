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
 * Create a new cli application.
 */
$app = new Symfony\Component\Console\Application;

/**
 * Populate the cli application.
 */
(require $root . '/app/cli.php')($container, $app);

/**
 * Run the cli application.
 */
$app->run();
