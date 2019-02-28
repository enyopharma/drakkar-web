<?php declare(strict_types=1);

use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;

use Enyo\Http\SessionMiddleware;
use Enyo\Http\CallableMiddleware;
use Enyo\Http\NotFoundMiddleware;
use Enyo\Http\HttpErrorMiddleware;
use Enyo\Http\HttpMethodMiddleware;

return [
    'parameters' => [
        'http.middleware.queue.factory.path' => '%{app.root}/config/middleware.php',
    ],

    'factories' => [
        'http.middleware.queue' => function ($container) {
            $factory = require $container->get('http.middleware.queue.factory.path');

            $middleware[] = $container->get(HttpErrorMiddleware::class);
            $middleware[] = $container->get(SessionMiddleware::class);
            $middleware[] = $container->get(HttpMethodMiddleware::class);

            $middleware = array_merge($middleware, $factory($container));

            $middleware[] = $container->get(RouteMiddleware::class);
            $middleware[] = $container->get(DispatchMiddleware::class);
            $middleware[] = $container->get(NotFoundMiddleware::class);

            return $middleware;
        },
    ],
];
