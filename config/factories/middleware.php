<?php declare(strict_types=1);

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Utils\Http\SessionMiddleware;
use Utils\Http\NotFoundMiddleware;
use Utils\Http\HttpErrorMiddleware;
use Utils\Http\HttpMethodMiddleware;

return [
    'factories' => [
        SessionMiddleware::class => function ($container) {
            return new SessionMiddleware(
                $container->get(SessionHandlerInterface::class)
            );
        },

        HttpErrorMiddleware::class => function ($container) {
            return new HttpErrorMiddleware(
                $container->get(StreamFactoryInterface::class)
            );
        },

        HttpMethodMiddleware::class => function () {
            return new HttpMethodMiddleware;
        },

        NotFoundMiddleware::class => function ($container) {
            return new NotFoundMiddleware(
                $container->get(ResponseFactoryInterface::class)
            );
        },
    ],
];
