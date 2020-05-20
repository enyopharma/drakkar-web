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
        'GET /publications' => function () use ($container) {
            return new App\Handlers\Publications\SearchHandler(
                $container->get(App\Responders\HtmlResponder::class),
                $container->get(App\ReadModel\PublicationViewInterface::class)
            );
        },

        'GET /runs/{run_id:\d+}/publications' => function () use ($container) {
            return new App\Handlers\Publications\IndexHandler(
                $container->get(App\Responders\HtmlResponder::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            );
        },

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => function () use ($container) {
            return new App\Handlers\Publications\UpdateHandler(
                $container->get(App\Responders\HtmlResponder::class),
                $container->get(App\Actions\UpdatePublicationStateInterface::class)
            );
        },
    ];
};
