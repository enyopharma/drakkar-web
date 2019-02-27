<?php declare(strict_types=1);

use Psr\Cache\CacheItemPoolInterface;

use Cache\SessionHandler\Psr6SessionCache;
use Cache\SessionHandler\Psr6SessionHandler;

use Shared\Http\Session;
use Shared\Http\SessionMiddleware;

return [
    'parameters' => [
        'session.handler.options' => [],
    ],

    'aliases' => [
        SessionHandlerInterface::class => Psr6SessionHandler::class,
        'session.cache' => CacheItemPoolInterface::class,
    ],

    'factories' => [
        Session::class => function () {
            return new Session;
        },

        Psr6SessionHandler::class => function ($container) {
            return new Psr6SessionHandler(
                $container->get('session.cache'),
                $container->get('session.handler.options')
            );
        },

        SessionMiddleware::class => function ($container) {
            return new SessionMiddleware(
                $container->get(Psr6SessionHandler::class),
                $container->get(Session::class)
            );
        },
    ],
];
