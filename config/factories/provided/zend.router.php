<?php declare(strict_types=1);

use Zend\Expressive\Router\RouteCollector;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;

return [
    'factories' => [
        'router.mapper' => function () {
            return function () {};
        },

        RouteMiddleware::class => function ($container) {
            $router = $container->get(RouterInterface::class);

            return new RouteMiddleware($router);
        },

        DispatchMiddleware::class => function () {
            return new DispatchMiddleware;
        },
    ],

    'extensions' => [
        RouterInterface::class => function ($container, RouterInterface $router) {
            $mapper = $container->get('router.mapper');

            $mapper(new RouteCollector($router));

            return $router;
        },
    ],
];
