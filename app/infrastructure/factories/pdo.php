<?php

declare(strict_types=1);

return [
    PDO::class => function ($container) {
        return new PDO(
            vsprintf('pgsql:host=%s;port=%s;dbname=%s', [
                $_ENV['DB_HOSTNAME'] ?? 'localhost',
                $_ENV['DB_PORT'] ?? '5432',
                $_ENV['DB_DATABASE'] ?? 'database',
            ]),
            $_ENV['DB_USERNAME'] ?? 'username',
            $_ENV['DB_PASSWORD'] ?? 'password',
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    },
];
