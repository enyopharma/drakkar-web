<?php declare(strict_types=1);

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Enyo\Http\SessionMiddleware;
use Enyo\Http\CallableMiddleware;
use Enyo\Http\NotFoundMiddleware;
use Enyo\Http\HttpErrorMiddleware;
use Enyo\Http\HttpMethodMiddleware;

return [
    'parameters' => [
        'http.middleware.queue.factory.path' => '%{app.root}/config/middleware.php',
    ],

    'factories' => [
        'http.middleware.queue' => function ($container) {
            $middleware = require $container->get('http.middleware.queue.factory.path');

            return $middleware($container);
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
