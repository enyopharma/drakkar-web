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

    /**
     * Get the response factory from the container.
     */
    $factory = $container->get(Psr\Http\Message\ResponseFactoryInterface::class);

    /**
     * Get the fast route dispatcher and build a router.
     */
    $router = new App\Http\FastRouteRouter(
        $container->get(FastRoute\Dispatcher::class)
    );

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
         * Parse json body.
         */
        new Middlewares\JsonPayload,

        /**
         * Router.
         */
        new Quanta\Http\RoutingMiddleware($router),

        /**
         * Return a not allowed response.
         */
        new Quanta\Http\NotAllowedMiddleware($factory),

        /**
         * Return a not found response.
         */
        new Quanta\Http\NotFoundMiddleware($factory),
    );
};
