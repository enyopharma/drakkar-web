<?php

declare(strict_types=1);

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;

return [
    RouterInterface::class => function ($container) {
        return $container->get(FastRouteRouter::class);
    },

    FastRouteRouter::class => function ($container) {
        $router = new FastRouteRouter;

        $collector = new Zend\Expressive\Router\RouteCollector($router);

        $mapper = require sprintf('%s/src/App/Http/routes.php', $container->get('app.root'));

        $mapper($container, $collector);

        return $router;
    },

    RouteMiddleware::class => function ($container) {
        return new RouteMiddleware(
            $container->get(RouterInterface::class)
        );
    },

    DispatchMiddleware::class => function () {
        return new DispatchMiddleware;
    },
];
