<?php declare(strict_types=1);

use Predis\Client;

return [
    Client::class => function ($container) {
        return $container->get('predis.clients.default');
    },

    'predis.clients.default' => function ($container) {
        $scheme = getenv('REDIS_SCHEME');
        $host = getenv('REDIS_HOST');
        $port = getenv('REDIS_PORT');

        return new Client([
            'scheme' => $scheme === false ? 'tcp' : $scheme,
            'host' => $host === false ? 'localhost' : $host,
            'port' => $port === false ? 6379 : (int) $port,
        ]);
    },
];
