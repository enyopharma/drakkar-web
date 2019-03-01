<?php declare(strict_types=1);

use Enyo\Http\RouteMapper;
use Enyo\Http\RouteHandlerFactory;
use Enyo\Http\RequestHandlerFactory;

return [
    'parameters' => [
        'router.mapper.path' => '%{app.root}/app/routes.php',
    ],

    'factories' => [
        RequestHandlerFactory::class => function ($container) {
            return new RequestHandlerFactory($container);
        },

        RouteHandlerFactory::class => function ($container) {
            return new RouteHandlerFactory(
                $container->get(RequestHandlerFactory::class)
            );
        },

        'router.mapper' => function ($container) {
            return require $container->get('router.mapper.path');
        },
    ],

    'extensions' => [
        'router.mapper' => function ($container, callable $mapper) {
            return new RouteMapper(
                $container->get(RouteHandlerFactory::class),
                $mapper
            );
        },
    ],
];
