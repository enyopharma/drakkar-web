<?php

declare(strict_types=1);

return [
    PDO::class => function ($container) {
        return $container->get('pdo.clients.default');
    },

    'pdo.clients.default' => function ($container) {
        return new PDO(
            vsprintf('pgsql:host=%s;port=%s;dbname=%s', [
                $container->get('db.hostname'),
                $container->get('db.port'),
                $container->get('db.database'),
            ]),
            $container->get('db.username'),
            $container->get('db.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    },
];
