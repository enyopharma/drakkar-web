<?php declare(strict_types=1);

return [
    'parameters' => [
        'predis.configurations' => [
            'default' => [
                'scheme' => 'env(REDIS_SCHEME, tcp)',
                'host' => 'env(REDIS_HOST, localhost)',
                'port' => 'env(REDIS_PORT, 6379)',
            ],
        ],
    ],
];
