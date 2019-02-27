<?php declare(strict_types=1);

use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;

use Shared\Http\SessionMiddleware;
use Shared\Http\CallableMiddleware;
use Shared\Http\NotFoundMiddleware;
use Shared\Http\HttpErrorMiddleware;
use Shared\Http\HttpMethodMiddleware;

return [
    'extensions' => [
        'http.middleware.queue' => function ($container, array $queue) {
            $middleware[] = $container->get(HttpErrorMiddleware::class);
            $middleware[] = $container->get(SessionMiddleware::class);
            $middleware[] = $container->get(HttpMethodMiddleware::class);

            $middleware = array_merge($middleware, $queue);

            $middleware[] = $container->get(RouteMiddleware::class);
            $middleware[] = $container->get(DispatchMiddleware::class);
            $middleware[] = $container->get(NotFoundMiddleware::class);

            return $middleware;
        },
    ],
];
