<?php declare(strict_types=1);

use Psr\Cache\CacheItemPoolInterface;

use Cache\Adapter\Predis\PredisCachePool;

return [
    'aliases' => [
        CacheItemPoolInterface::class => PredisCachePool::class,
    ],

    'factories' => [
        PredisCachePool::class => function ($container) {
            return new PredisCachePool(
                $container->get(Predis\Client::class)
            );
        },
    ],
];
