<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Return the route definitions.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array[]
 */
return function (ContainerInterface $container): array {
    return [
        'GET /methods' => function () use ($container) {
            return new App\Http\Handlers\Methods\IndexHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\MethodViewInterface::class)
            );
        },

        'GET /methods/{psimi_id}' => function () use ($container) {
            return Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\Methods\ShowHandler(
                    $container->get(App\Http\Responders\JsonResponder::class)
                ),
                new App\Http\Middleware\FetchMethodMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                    $container->get(Domain\ReadModel\MethodViewInterface::class)
                )
            );
        },
    ];
};
