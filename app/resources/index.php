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
        'GET /' => fn () => new App\Handlers\Runs\IndexHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
        ),
    ];
};
