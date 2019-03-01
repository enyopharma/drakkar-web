<?php declare(strict_types=1);

use Enyo\Data\StatementMap;

return [
    'parameters' => [
        'sql.queries' => [],
    ],

    'aliases' => [
        'sql.pdo.client' => PDO::class,
    ],

    'factories' => [
        StatementMap::class => function ($container) {
            return new StatementMap(
                $container->get('sql.pdo.client'),
                $container->get('sql.queries')
            );
        },
    ],
];
