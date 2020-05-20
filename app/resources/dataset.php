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
        'GET /dataset/{type:hh|vh}' => fn () => new App\Handlers\Dataset\DownloadHandler(
            $container->get(App\Responders\FileResponder::class),
            $container->get(App\ReadModel\DatasetViewInterface::class),
        ),
    ];
};
