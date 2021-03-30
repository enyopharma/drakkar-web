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
    $generator = $container->get(Quanta\Http\UrlGenerator::class);

    $routes = new App\Sources\CallableSource(
        new App\Sources\PHPFileSource(__DIR__ . '/../routes/*.php'),
        $container,
    );

    foreach ($routes as $route) {
        if (!$route instanceof Quanta\Http\Route) {
            throw new UnexpectedValueException('iterable returned by the route definition callable must contain only Route instances');
        }

        $methods = $route->methods();
        $pattern = $route->pattern();
        $handler = $route->handler();

        $collector->addRoute($methods, $pattern, $handler);

        if ($route->hasName()) {
            $generator->register($route->name(), $pattern);
        }
    }
};
