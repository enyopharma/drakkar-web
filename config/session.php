<?php declare(strict_types=1);

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;

use Cache\SessionHandler\Psr6SessionHandler;

return function (ContainerInterface $container) {
    $cache = $container->get(CacheItemPoolInterface::class);

    if (($ttl = getenv('SESSION_TTL')) !== false) {
        $options['ttl'] = (int) $ttl;
    }

    if (($prefix = getenv('SESSION_PREFIX')) !== false) {
        $options['prefix'] = $prefix;
    }

    session_set_save_handler(new Psr6SessionHandler($cache, $options ?? []));
};
