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

        'GET /methods/{psimi_id}' => fn () => new App\Handlers\Methods\ShowHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\MethodViewInterface::class),
        ),
    ];
};
