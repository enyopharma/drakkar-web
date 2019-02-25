<?php declare(strict_types=1);

return [
    'parameters' => [
        'pdo.configurations' => [
            'default' => [
                'dsn' => 'env(DB_DSN, pgsql:host=localhost;port=5432;dbname=database)',
                'username' => 'env(DB_USERNAME, username)',
                'password' => 'env(DB_PASSWORD, secret)',
                'options' => [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ],
            ],
        ],
    ],
];
