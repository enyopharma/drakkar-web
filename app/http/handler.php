<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Psr\Http\Server\RequestHandlerInterface;

/**
 * A factory producing the application request handler.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return Psr\Http\Server\RequestHandlerInterface
 */
return function (ContainerInterface $container): RequestHandlerInterface {
    if (file_exists(__DIR__ . '/shutdown')) {
        return Quanta\Http\Dispatcher::queue(new Middlewares\Shutdown);
    }

    return Quanta\Http\Dispatcher::queue(
        /**
         * Whoops error handler.
         */
        new Middlewares\Whoops,

        /**
         * Override the post method
         */
        (new Middlewares\MethodOverride)->parsedBodyParameter('_method'),

        /**
         * Json body parser.
         */
        new Middlewares\JsonPayload,

        /**
         * Router.
         */
        new Zend\Expressive\Router\Middleware\RouteMiddleware(
            $container->get(Zend\Expressive\Router\RouterInterface::class)
        ),

        /**
         * Route dispatcher.
         */
        new Zend\Expressive\Router\Middleware\DispatchMiddleware,
    );
};
