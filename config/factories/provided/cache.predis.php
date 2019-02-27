<?php declare(strict_types=1);

use Psr\Cache\CacheItemPoolInterface;

use Predis\Client;

use Cache\Adapter\Predis\PredisCachePool;
use Cache\Adapter\Predis\PredisCacheClient;

return [
    'aliases' => [
        CacheItemPoolInterface::class => PredisCachePool::class,
        'cache.predis.client' => Client::class,
    ],

    'factories' => [
        PredisCachePool::class => function ($container) {
            return new PredisCachePool(
                $container->get('cache.predis.client')
            );
        },
    ],
];
