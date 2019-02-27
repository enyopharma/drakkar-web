<?php declare(strict_types=1);

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Shared\Http\NotFoundMiddleware;
use Shared\Http\HttpErrorMiddleware;
use Shared\Http\HttpMethodMiddleware;

return [
    'factories' => [
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
