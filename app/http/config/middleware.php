<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Return an array of middleware.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return Psr\Http\Server\MiddlewareInterface[]
 */
return function (ContainerInterface $container): array {
    if (file_exists(__DIR__ . '/../shutdown')) {
        return [
            new Middlewares\Shutdown,
        ];
    }

    $factory = $container->get(Psr\Http\Message\ResponseFactoryInterface::class);

    return [
        /**
         * Whoops error handler.
         */
        new Middlewares\Whoops,

        /**
         * SSO auth.
         */
        new App\Http\Middleware\SsoAuthentificationMiddleware($_ENV['SSO_HOST'], $factory),

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
    ];
};
