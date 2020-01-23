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
        'GET /proteins' => function () use ($container) {
            return new App\Http\Handlers\Proteins\IndexHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\ProteinViewInterface::class)
            );
        },

        'GET /proteins/{accession}' => function () use ($container) {
            return Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\Proteins\ShowHandler(
                    $container->get(App\Http\Responders\JsonResponder::class)
                ),
                new App\Http\Middleware\FetchProteinMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                    $container->get(Domain\ReadModel\ProteinViewInterface::class)
                )
            );
        },
    ];
};
