<?php declare(strict_types=1);

use Predis\Client;

use Enyo\Clients\PredisClientPool;

return [
    'parameters' => [
        'predis.configurations' => [
            'default' => [
                'scheme' => 'tcp',
                'host' => 'localhost',
                'port' => '6379',
            ],
        ],
    ],

    'factories' => [
        PredisClientPool::class => function ($container) {
            return new PredisClientPool(
                $container->get('predis.configurations')
            );
        },

        Client::class => function ($container) {
            return $container->get(PredisClientPool::class)->client('default');
        },
    ],
];
