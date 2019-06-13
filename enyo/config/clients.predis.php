<?php declare(strict_types=1);

use Predis\Client;

return [
    'parameters' => [
        'predis.configurations.default' => [
            'scheme' => 'env(REDIS_SCHEME, tcp)',
            'host' => 'env(REDIS_HOST, localhost)',
            'port' => 'env(REDIS_PORT, 6379)',
        ],
    ],

    'aliases' => [
        Client::class => 'predis.clients.default',
    ],

    'factories' => [
        'predis.clients.default' => function ($container) {
            return new Client(
                $container->get('predis.configurations.default')
            );
        },
    ],
];
