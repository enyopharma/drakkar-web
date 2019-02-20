<?php declare(strict_types=1);

use Utils\Http\RouteMapper;

return [
    'parameters' => [
        'router.mapper.path' => '%{app.root}/config/routes.php',
    ],

    'factories' => [
        'router.mapper' => function ($container) {
            $mapper = require $container->get('router.mapper.path');

            return new RouteMapper($container, $mapper);
        },
    ],
];
