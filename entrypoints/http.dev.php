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
 * Register the exception handler.
 */
Quanta\Http\ExceptionHandler::register(true, true);

/**
 * Get a http server in develoment environement with debug mode enabled.
 */
$server = new Quanta\Http\Server(
    new App\Http\Server\NyholmContext(
        require $root . '/http/app.php'
    )
);

/**
 * Run the http server.
 */
$server->run($root, 'develoment', true);
