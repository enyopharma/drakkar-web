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
$map = (require $root . '/config/map.php')($app);
$factories = (require $root . '/config/factories.php')($map);
$container = (require $root . '/config/container.php')($factories);

/**
 * Run the cli application.
 */
$container->get(Symfony\Component\Console\Application::class)->run();
