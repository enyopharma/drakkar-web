<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;

use App\Http\Middleware\NotFoundMiddleware;
use App\Http\Middleware\HttpMethodMiddleware;
use App\Http\Middleware\HttpSourceMiddleware;
use App\Http\Middleware\SsoAuthentificationMiddleware;

return [
    NotFoundMiddleware::class => function ($container) {
        return new NotFoundMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    },

    HttpMethodMiddleware::class => function () {
        return new HttpMethodMiddleware;
    },

    HttpSourceMiddleware::class => function () {
        return new HttpSourceMiddleware;
    },

    SsoAuthentificationMiddleware::class => function ($container) {
        return new SsoAuthentificationMiddleware(
            $container->get('sso.host'),
            $container->get(ResponseFactoryInterface::class)
        );
    },
];
