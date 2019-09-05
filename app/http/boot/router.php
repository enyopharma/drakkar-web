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
    $collector = new Zend\Expressive\Router\RouteCollector(
        $container->get(Zend\Expressive\Router\RouterInterface::class)
    );

    $factory = function (string $action, string ...$responders) use ($container) {
        return new App\Http\Middleware\LazyMiddleware(function () use ($container, $action, $responders) {
            return new App\Http\Middleware\HandlerMiddleware(
                new App\Http\Input\HttpInput,
                $container->get($action),
                $container->get(App\Http\Responders\JsonResponder::class),
                ...array_map(function ($responder) use ($container) {
                    return $container->get($responder);
                }, $responders)
            );
        });
    };

    $routes = (require __DIR__ . '/../config/routes.php')($factory);

    foreach ($routes as $endpoint => $route) {
        $parts = (array) preg_split('/\s+/', $endpoint);

        if (count($parts) != 2) {
            throw new LogicException(sprintf('invalid endpoint \'%s\'', $endpoint));
        }

        if (! key_exists('handler', $route)) {
            throw new LogicException(sprintf('missing handler for endpoint \'%s\'', $endpoint));
        }

        $method = (string) array_shift($parts);
        $path = (string) array_shift($parts);
        $name = $route['name'] ?? null;
        $handler = $route['handler'];

        $collector->route($path, $handler, [$method], $name);
    }
};
