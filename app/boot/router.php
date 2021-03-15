<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Populate the router.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return void
 */
return function (ContainerInterface $container) {
    $collector = $container->get(FastRoute\RouteCollector::class);
    $generator = $container->get(App\Routing\UrlGenerator::class);

    $files = glob(__DIR__ . '/../routes/*.php');

    if ($files === false) {
        throw new Exception;
    }

    foreach ($files as $file) {
        $provider = require $file;

        if (!is_callable($provider)) {
            throw new UnexpectedValueException('route definition file must return a callable');
        }

        $routes = $provider($container);

        if (!is_iterable($routes)) {
            throw new UnexpectedValueException('route definition callable must return an iterable');
        }

        foreach ($routes as $route) {
            if (!$route instanceof App\Routing\Route) {
                throw new UnexpectedValueException('iterable returned by the route definition callable must contain only Route instances');
            }

            $methods = $route->methods();
            $pattern = $route->pattern();
            $handler = $route->handler();

            $collector->addRoute($methods, $pattern, $handler);

            if ($route->isNamed()) {
                $generator->register($route->name(), $pattern);
            }
        }
    }
};
