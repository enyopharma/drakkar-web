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
        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => function () use ($container) {
            return Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\Descriptions\IndexHandler(
                    $container->get(App\Http\Responders\HtmlResponder::class)
                ),
                new App\Http\Middleware\FetchRunMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                    $container->get(Domain\ReadModel\RunViewInterface::class)
                ),
                new App\Http\Middleware\FetchPublicationMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
                )
            );
        },

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => function () use ($container) {
            return Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\Descriptions\CreateHandler(
                    $container->get(App\Http\Responders\HtmlResponder::class),
                ),
                new App\Http\Middleware\FetchRunMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                    $container->get(Domain\ReadModel\RunViewInterface::class)
                ),
                new App\Http\Middleware\FetchPublicationMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
                )
            );
        },

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => function () use ($container) {
            return Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\Descriptions\EditHandler(
                    $container->get(App\Http\Responders\HtmlResponder::class),
                ),
                new App\Http\Middleware\FetchRunMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                    $container->get(Domain\ReadModel\RunViewInterface::class)
                ),
                new App\Http\Middleware\FetchPublicationMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
                ),
                new App\Http\Middleware\FetchDescriptionMiddleware(
                    $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
                )
            );
        },

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => function () use ($container) {
            return Quanta\Http\RequestHandler::queue(
                new App\Http\Handlers\Descriptions\StoreHandler(
                    $container->get(App\Http\Responders\JsonResponder::class),
                    $container->get(Domain\Actions\StoreDescriptionInterface::class)
                ),
                new App\Http\Middleware\ValidateDescriptionMiddleware(
                    $container->get(App\Http\Responders\JsonResponder::class),
                    $container->get(PDO::class)
                )
            );
        },

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => function () use ($container) {
            return new App\Http\Handlers\Descriptions\DeleteHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\Actions\DeleteDescriptionInterface::class)
            );
        },
    ];
};
