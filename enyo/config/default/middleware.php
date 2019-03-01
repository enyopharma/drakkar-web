<?php declare(strict_types=1);

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

return [
    'parameters' => [
        'http.middleware.queue.factory.path' => '%{app.root}/config/middleware.php',
    ],

    'factories' => [
        'http.middleware.queue.factory' => function ($container) {
            return require $container->get('http.middleware.queue.factory.path');
        },

        'http.middleware.queue' => function ($container) {
            return $container->get('http.middleware.queue.factory')($container);
        },

        Enyo\Http\HttpErrorMiddleware::class => function ($container) {
            return new Enyo\Http\HttpErrorMiddleware(
                $container->get(StreamFactoryInterface::class)
            );
        },

        Enyo\Http\HttpMethodMiddleware::class => function () {
            return new Enyo\Http\HttpMethodMiddleware;
        },

        Enyo\Http\NotFoundMiddleware::class => function ($container) {
            return new Enyo\Http\NotFoundMiddleware(
                $container->get(ResponseFactoryInterface::class)
            );
        },
    ],
];
