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

    $routes = (require __DIR__ . '/../routes.php')();

    foreach ($routes as $endpoint => $route) {
        $parts = (array) preg_split('/\s+/', $endpoint);

        if (count($parts) != 2) {
            throw new LogicException(sprintf('invalid endpoint \'%s\'', $endpoint));
        }

        if (! key_exists('action', $route)) {
            throw new LogicException(sprintf('missing action for endpoint \'%s\'', $endpoint));
        }

        $method = (string) array_shift($parts);
        $path = (string) array_shift($parts);
        $name = $route['name'] ?? null;
        $action = $route['action'];
        $responder = $route['responder'] ?? App\Http\Responders\JsonResponder::class;

        $middleware = new App\Http\Middleware\RequestHandlerMiddleware(
            new App\Http\Handlers\LazyRequestHandler(function () use ($container, $action, $responder) {
                return new App\Http\Handlers\Endpoint(
                    new App\Http\Input\HttpInput,
                    $container->get($action),
                    $container->get($responder)
                );
            })
        );

        $collector->route($path, $middleware, [$method], $name);
    }
};
