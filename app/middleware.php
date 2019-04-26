<?php declare(strict_types=1);

use Enyo\Http\MiddlewareFactory;
use Enyo\Http\SessionMiddleware;
use Enyo\Http\NotFoundMiddleware;
use Enyo\Http\HttpMethodMiddleware;

use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;

return function (MiddlewareFactory $factory) {
    return [
        new Middlewares\JsonPayload,
        $factory(SessionMiddleware::class),
        $factory(HttpMethodMiddleware::class),
        $factory(RouteMiddleware::class),
        $factory(DispatchMiddleware::class),
        $factory(NotFoundMiddleware::class),
    ];
};
