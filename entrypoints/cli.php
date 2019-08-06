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
 * Build the app container.
 */
$container = require $root . '/container.php';

/**
 * Run the cli application.
 */
$container->get(Symfony\Component\Console\Application::class)->run();
