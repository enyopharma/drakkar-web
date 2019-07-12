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
[$env, $debug] = (require $root . '/config/envvars.php')($root);

/**
 * Register an error handler.
 */
$handler = Quanta\ErrorHandler\register()->setRenderer(
    new Quanta\ErrorHandler\CliRenderer
);

/**
 * Build the app container.
 */
$container = (require $root . '/config/container.php')($root, $env, $debug);

/**
 * Run the cli application.
 */
$container->get(Symfony\Component\Console\Application::class)->run();
