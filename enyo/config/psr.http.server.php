<?php declare(strict_types=1);

use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\FIFODispatcher;

use Enyo\Http\Handlers\RequestHandlerFactory;
use Enyo\Http\Middleware\MiddlewareFactory;

return [
    'parameters' => [
        'http.middleware.queue.factory.path' => '%{app.root}/app/http.php',
    ],

    'aliases' => [
        RequestHandlerInterface::class => FIFODispatcher::class,
    ],

    'factories' => [
        'http.middleware.queue.factory' => function ($container) {
            return require $container->get('http.middleware.queue.factory.path');
        },

        'http.middleware.queue' => function ($container) {
            return $container->get('http.middleware.queue.factory')(
                $container->get(MiddlewareFactory::class)
            );
        },

        MiddlewareFactory::class => function ($container) {
            return new MiddlewareFactory($container);
        },

        RequestHandlerFactory::class => function ($container) {
            return new RequestHandlerFactory($container);
        },

        FIFODispatcher::class => function ($container) {
            return new FIFODispatcher(
                new Enyo\Http\Handlers\InnerMostRequestHandler,
                ...$container->get('http.middleware.queue')
            );
        },
    ],
];
