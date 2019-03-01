<?php declare(strict_types=1);

use Enyo\Http\RouteMapper;

return [
    'parameters' => [
        'router.mapper.path' => '%{app.root}/config/routes.php',
    ],

    'factories' => [
        'router.mapper' => function ($container) {
            return new RouteMapper(
                require $container->get('router.mapper.path')
            );
        },
    ],
];
