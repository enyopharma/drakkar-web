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
        'POST /jobs/alignments' => function () use ($container) {
            return new App\Http\Handlers\Alignments\StartHandler(
                $container->get(Predis\Client::class),
                $container->get(App\Http\Responders\JsonResponder::class)
            );
        },
    ];
};