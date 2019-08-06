<?php

declare(strict_types=1);

use Predis\Client;

return [
    Client::class => function ($container) {
        return $container->get('predis.clients.default');
    },

    'predis.clients.default' => function ($container) {
        return new Client([
            'scheme' => $container->get('redis.scheme'),
            'host' => $container->get('redis.host'),
            'port' => $container->get('redis.port'),
        ]);
    },
];
