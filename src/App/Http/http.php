<?php

declare(strict_types=1);

use Enyo\Http\Middleware\MiddlewareFactory;

return function (MiddlewareFactory $factory) {
    $middleware = [];

    // conditional shutdown middleware.
    if (file_exists(__DIR__ . '/shutdown')) {
        $middleware[] = new Middlewares\Shutdown;
    }

    // return the middleware list.
    return array_merge($middleware, [
        new Middlewares\JsonPayload,
        $factory(Enyo\Http\Middleware\SsoAuthentificationMiddleware::class),
        $factory(Enyo\Http\Middleware\HttpMethodMiddleware::class),
        $factory(Enyo\Http\Middleware\HttpSourceMiddleware::class),
        $factory(Zend\Expressive\Router\Middleware\RouteMiddleware::class),
        $factory(Zend\Expressive\Router\Middleware\DispatchMiddleware::class),
        $factory(App\Http\Middleware\NotFoundMiddleware::class),
    ]);
};
