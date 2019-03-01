<?php declare(strict_types=1);

use Enyo\Http\MiddlewareFactory;

return [
    'parameters' => [
        'http.middleware.queue.factory.path' => '%{app.root}/app/middleware.php',
    ],

    'factories' => [
        MiddlewareFactory::class => function ($container) {
            return new MiddlewareFactory($container);
        },

        'http.middleware.queue.factory' => function ($container) {
            return require $container->get('http.middleware.queue.factory.path');
        },

        'http.middleware.queue' => function ($container) {
            return $container->get('http.middleware.queue.factory')(
                $container->get(MiddlewareFactory::class)
            );
        },
    ],
];
