<?php declare(strict_types=1);

use Zend\Expressive\Helper\UrlHelper;
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
            return new RouteMiddleware(
                $container->get(RouterInterface::class)
            );
        },

        DispatchMiddleware::class => function () {
            return new DispatchMiddleware;
        },

        UrlHelper::class => function ($container) {
            return new UrlHelper(
                $container->get(RouterInterface::class)
            );
        },
    ],

    'extensions' => [
        RouterInterface::class => function ($container, RouterInterface $router) {
            $mapper = $container->get('router.mapper');

            $mapper($container, new RouteCollector($router));

            return $router;
        },
    ],
];
