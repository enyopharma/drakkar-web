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
        'GET /' => function () use ($container) {
            return new App\Http\Handlers\Runs\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );
        },
    ];
};
