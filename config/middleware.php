<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;

use Utils\Http\CallableMiddleware;
use Utils\Http\NotFoundMiddleware;
use Utils\Http\HttpErrorMiddleware;

return function (ContainerInterface $container) {
    return [
        $container->get(HttpErrorMiddleware::class),
        $container->get(RouteMiddleware::class),
        $container->get(DispatchMiddleware::class),
        $container->get(NotFoundMiddleware::class),
    ];
};
