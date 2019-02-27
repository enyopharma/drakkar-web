<?php declare(strict_types=1);

use Utils\Clients\PDOClientPool;

return [
    'parameters' => [
        'pdo.configurations' => [
            'default' => [
                'dsn' => 'pgsql:host=localhost;port=5432;dbname=database',
                'username' => 'username',
                'password' => 'secret',
                'options' => [],
            ],
        ],
    ],

    'factories' => [
        PDOClientPool::class => function ($container) {
            return new PDOClientPool(
                $container->get('pdo.configurations')
            );
        },

        \PDO::class => function ($container) {
            return $container->get(PDOClientPool::class)->client('default');
        },
    ],
];
