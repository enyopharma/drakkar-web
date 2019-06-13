<?php declare(strict_types=1);

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

return [
    'parameters' => [
        'router.mapper.path' => '%{app.root}/app/routes.php',
    ],

    'aliases' => [
        RouterInterface::class => FastRouteRouter::class,
    ],

    'factories' => [
        FastRouteRouter::class => function ($container) {
            $router = new FastRouteRouter;

            $mapper = new Enyo\Http\Routing\RouteMapper(
                new Enyo\Http\Routing\RouteHandlerFactory(
                    $container->get(Enyo\Http\Handlers\RequestHandlerFactory::class)
                ),
                require $container->get('router.mapper.path')
            );

            $mapper(new Zend\Expressive\Router\RouteCollector($router));

            return $router;
        },
    ],
];
