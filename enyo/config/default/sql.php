<?php declare(strict_types=1);

return [
    'parameters' => [
        'sql.queries.path' => '%{app.root}/app/queries.php',
    ],

    'factories' => [
        'sql.queries' => function ($container) {
            return require $container->get('sql.queries.path');
        },
    ],
];