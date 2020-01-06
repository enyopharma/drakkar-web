<?php

declare(strict_types=1);

/**
 * Set up the autoloader.
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * Complete the env with local values.
 */
(new Symfony\Component\Dotenv\Dotenv(false))->load(__DIR__ . '/../.env');

/**
 * Get the env and debug mod from the env var.
 */
$env = $_ENV['APP_ENV'] ?? 'production';
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

/**
 * Get the container.
 */
$container = (require __DIR__ . '/../app/http/container.php')($env, $debug);

/**
 * Run the boot scripts.
 */
foreach ((array) glob(__DIR__ . '/../app/http/boot/*.php') as $boot) {
    (require $boot)($container);
}

/**
 * Get the http request handler.
 */
$handler = (require __DIR__ . '/../app/http/handler.php')($container);

/**
 * Run the application.
 */
use function Http\Response\send;

$factory = new Nyholm\Psr7\Factory\Psr17Factory;
$creator = new Nyholm\Psr7Server\ServerRequestCreator($factory, $factory, $factory, $factory);

$request = $creator->fromGlobals();

$response = $handler->handle($request);

send($response);
