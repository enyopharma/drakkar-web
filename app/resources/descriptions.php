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
        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => new App\Handlers\Descriptions\IndexHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
            $container->get(App\ReadModel\DescriptionViewInterface::class),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => fn () => new App\Handlers\Descriptions\CreateHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => fn () => new App\Handlers\Descriptions\EditHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
            $container->get(App\ReadModel\DescriptionViewInterface::class),
        ),

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => Quanta\Http\RequestHandler::queue(
            new App\Handlers\Descriptions\StoreHandler(
                $container->get(App\Responders\JsonResponder::class),
                $container->get(App\Actions\StoreDescriptionInterface::class),
            ),
            new App\Middleware\ValidateDescriptionMiddleware(
                $container->get(App\Responders\JsonResponder::class),
                $container->get(PDO::class),
            ),
        ),

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => fn () => new App\Handlers\Descriptions\DeleteHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\Actions\DeleteDescriptionInterface::class),
        ),
    ];
};
