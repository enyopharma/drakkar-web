<?php declare(strict_types=1);

use Psr\Cache\CacheItemPoolInterface;

use Cache\Adapter\Predis\PredisCachePool;

return [
    CacheItemPoolInterface::class => function ($container) {
        return $container->get(PredisCachePool::class);
    },

    PredisCachePool::class => function ($container) {
        return new PredisCachePool(
            $container->get(Predis\Client::class)
        );
    },
];
