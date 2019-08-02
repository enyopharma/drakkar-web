<?php

declare(strict_types=1);

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

return [
    RouterInterface::class => function ($container) {
        return $container->get(FastRouteRouter::class);
    },

    FastRouteRouter::class => function ($container) {
        $router = new FastRouteRouter;

        $mapper = new Enyo\Http\Routing\RouteMapper(
            new Enyo\Http\Routing\RouteHandlerFactory(
                new Enyo\Http\Handlers\RequestHandlerFactory($container)
            ),
            require sprintf('%s/src/App/Http/routes.php', $container->get('app.root'))
        );

        $mapper(new Zend\Expressive\Router\RouteCollector($router));

        return $router;
    },
];
