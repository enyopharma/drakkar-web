<?php declare(strict_types=1);

return [
    'extensions' => [
        PDO::class => function ($container, PDO $pdo) {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        },
    ],
];
