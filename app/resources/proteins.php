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
        'GET /proteins' => fn () => new App\Handlers\Proteins\IndexHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ),

        'GET /proteins/{accession}' => fn () => Quanta\Http\RequestHandler::queue(
            new App\Handlers\Proteins\ShowHandler(
                $container->get(App\Responders\JsonResponder::class),
            ),
            new App\Middleware\FetchProteinMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(App\ReadModel\ProteinViewInterface::class),
            ),
        ),
    ];
};
