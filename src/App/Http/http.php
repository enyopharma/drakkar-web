<?php

declare(strict_types=1);

return function (Psr\Container\ContainerInterface $container) {
    $middleware = [];

    // conditional shutdown middleware.
    if (file_exists($container->get('app.root') . '/shutdown')) {
        $middleware[] = $container->get(Middlewares\Shutdown::class);
    }

    // return the middleware list.
    return array_merge($middleware, [
        $container->get(Middlewares\JsonPayload::class),
        $container->get(App\Http\Middleware\SsoAuthentificationMiddleware::class),
        $container->get(App\Http\Middleware\HttpMethodMiddleware::class),
        $container->get(App\Http\Middleware\HttpSourceMiddleware::class),
        $container->get(Zend\Expressive\Router\Middleware\RouteMiddleware::class),
        $container->get(Zend\Expressive\Router\Middleware\DispatchMiddleware::class),
        $container->get(App\Http\Middleware\NotFoundMiddleware::class),
    ]);
};
