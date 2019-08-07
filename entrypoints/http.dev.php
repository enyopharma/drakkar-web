<?php

declare(strict_types=1);

error_reporting(-1);

/**
 * Get the root path.
 */
$root = (string) realpath(__DIR__ . '/../');

/**
 * Setup the autoloader.
 */
require $root . '/vendor/autoload.php';

/**
 * Get a http server in develoment environement with debug mode enabled.
 */
$server = (require $root . '/http/server.php')($root, 'develoment', true);

/**
 * Run the http server.
 */
$server->run();
