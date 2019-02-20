<?php declare(strict_types=1);

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Utils\Http\NotFoundMiddleware;
use Utils\Http\HttpErrorMiddleware;

return [
    'factories' => [
        HttpErrorMiddleware::class => function ($container) {
            $factory = $container->get(StreamFactoryInterface::class);

            return new HttpErrorMiddleware($factory);
        },

        NotFoundMiddleware::class => function ($container) {
            $factory = $container->get(ResponseFactoryInterface::class);

            return new NotFoundMiddleware($factory);
        },
    ],
];
