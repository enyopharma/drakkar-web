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

    $paths = (array) glob(__DIR__ . '/../resources/*.php');

    foreach ($paths as $path) {
        $definitions = (require $path)($container);

        foreach ($definitions as $route => $factory) {
            $routes[$route] = new App\Handlers\LazyRequestHandler($factory);
        }
    }

    return $routes;
};
