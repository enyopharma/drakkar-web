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
        'GET /methods' => fn () => new App\Handlers\Methods\IndexHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\MethodViewInterface::class),
        ),

        'GET /methods/{psimi_id}' => fn () => Quanta\Http\RequestHandler::queue(
            new App\Handlers\Methods\ShowHandler(
                $container->get(App\Responders\JsonResponder::class),
            ),
            new App\Middleware\FetchMethodMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(App\ReadModel\MethodViewInterface::class),
            ),
        ),
    ];
};
