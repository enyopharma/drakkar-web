<?php

declare(strict_types=1);

return [
    PDO::class => function ($container) {
        return $container->get('pdo.clients.default');
    },

    'pdo.clients.default' => function ($container) {
        $hostname = getenv('DB_HOSTNAME');
        $port = getenv('DB_PORT');
        $database = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        return new PDO(
            vsprintf('pgsql:host=%s;port=%s;dbname=%s', [
                $hostname === false ? 'localhost' : $hostname,
                $port === false ? 5432 : $port,
                $database === false ? 'database' : $database,
            ]),
            $username === false ? 'username' : $username,
            $password === false ? 'password' : $password,
            $options
        );
    },
];
