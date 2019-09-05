<?php

declare(strict_types=1);

use Predis\Client;

use Infrastructure\FactoryWithEnv;

return [
    Client::class => function ($container) {
        return $container->get('predis.clients.default');
    },

    'predis.clients.default' => function ($container) {
        return new Client([
            'scheme' => $_ENV['REDIS_SCHEME'] ?? 'tcp',
            'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
            'port' => $_ENV['REDIS_PORT'] ?? '6379',
        ]);
    },
];
