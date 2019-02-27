<?php declare(strict_types=1);

use Psr\Cache\CacheItemPoolInterface;

use Cache\SessionHandler\Psr6SessionCache;
use Cache\SessionHandler\Psr6SessionHandler;

return [
    'parameters' => [
        'session.handler.options' => [],
    ],

    'aliases' => [
        SessionHandlerInterface::class => Psr6SessionHandler::class,
        'session.cache' => CacheItemPoolInterface::class,
    ],

    'factories' => [
        Psr6SessionHandler::class => function ($container) {
            return new Psr6SessionHandler(
                $container->get('session.cache'),
                $container->get('session.handler.options')
            );
        },
    ],
];
