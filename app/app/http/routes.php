<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Return the route definitions.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array[]
 */
return function (ContainerInterface $container): array {
    $routes = [];

    $paths = ($tmp = glob(__DIR__ . '/resources/*.php')) === false ? [] : $tmp;

    foreach ($paths as $path) {
        $definitions = (require $path)($container);

        foreach ($definitions as $route => $factory) {
            $routes[$route] = Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\LazyRequestHandler($factory),
                new Middlewares\JsonPayload
            );
        }
    }

    return $routes;
};
