<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Return an array of middleware factories.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return callable[]
 */
return function (ContainerInterface $container): array {
    return [
        /**
         * Whoops error handler.
         */
        function () {
            return new Middlewares\Whoops;
        },

        /**
         * Shutdown middleware.
         */
        function () use ($container) {
             return new App\Http\Middleware\ShutdownMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                function () { return file_exists(__DIR__ . '/../shutdown'); }
            );
        },

        /**
         * SSO auth.
         */
        function () use ($container) {
            return new App\Http\Middleware\SsoAuthentificationMiddleware(
                $_ENV['SSO_HOST'],
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
            );
        },

        /**
         * Override the post method
         */
        function () {
            return (new Middlewares\MethodOverride)->parsedBodyParameter('_method');
        },

        /**
         * Json body parser.
         */
        function () {
            return new Middlewares\JsonPayload;
        },

        /**
         * Router.
         */
        function () use ($container) {
            return new Zend\Expressive\Router\Middleware\RouteMiddleware(
                $container->get(Zend\Expressive\Router\RouterInterface::class)
            );
        },

        /**
         * Route dispatcher.
         */
        function () {
            return new Zend\Expressive\Router\Middleware\DispatchMiddleware;
        },

        /**
         * Not found.
         */
        function () use ($container) {
            return new App\Http\Middleware\NotFoundMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
            );
        },
    ];
};
