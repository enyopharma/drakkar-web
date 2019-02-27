<?php declare(strict_types=1);

use Shared\Http\RouteMapper;

return [
    'parameters' => [
        'router.mapper.path' => '%{app.root}/config/routes.php',
    ],

    'factories' => [
        'router.mapper' => function ($container) {
            return require $container->get('router.mapper.path');
        },
    ],
];
