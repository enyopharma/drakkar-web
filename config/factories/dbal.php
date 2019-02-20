<?php declare(strict_types=1);

return [
    'parameters' => [
        'dbal.parameters.default' => [
            'host' => 'env(DB_HOSTNAME|localhost)',
            'dbname' => 'env(DB_DATABASE|database)',
            'user' => 'env(DB_USERNAME|username)',
            'password' => 'env(DB_PASSWORD|secret)',
        ],
    ],
];
