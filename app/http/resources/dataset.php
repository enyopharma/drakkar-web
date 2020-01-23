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
        'GET /dataset/{type:hh|vh}' => function () use ($container) {
            return new App\Http\Handlers\Dataset\DownloadHandler(
                $container->get(App\Http\Responders\FileResponder::class),
                $container->get(Domain\ReadModel\DatasetViewInterface::class)
            );
        },
    ];
};
