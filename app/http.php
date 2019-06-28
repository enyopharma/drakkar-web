<?php declare(strict_types=1);

use Enyo\Http\Middleware\MiddlewareFactory;

return function (MiddlewareFactory $factory) {
    return [
        $factory(Middlewares\JsonPayload::class),
        $factory(Enyo\Http\Middleware\SsoAuthentificationMiddleware::class),
        $factory(Enyo\Http\Middleware\HttpMethodMiddleware::class),
        $factory(Zend\Expressive\Router\Middleware\RouteMiddleware::class),
        $factory(Zend\Expressive\Router\Middleware\DispatchMiddleware::class),
        $factory(Enyo\Http\Middleware\NotFoundMiddleware::class),
    ];
};
