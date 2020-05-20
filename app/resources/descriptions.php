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
        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => Quanta\Http\RequestHandler::queue(
            new App\Handlers\Descriptions\IndexHandler(
                $container->get(App\Responders\HtmlResponder::class),
            ),
            new App\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(App\ReadModel\RunViewInterface::class),
            ),
            new App\Middleware\FetchPublicationMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => fn () => Quanta\Http\RequestHandler::queue(
            new App\Handlers\Descriptions\CreateHandler(
                $container->get(App\Responders\HtmlResponder::class),
            ),
            new App\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(App\ReadModel\RunViewInterface::class),
            ),
            new App\Middleware\FetchPublicationMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => fn () => Quanta\Http\RequestHandler::queue(
            new App\Handlers\Descriptions\EditHandler(
                $container->get(App\Responders\HtmlResponder::class),
            ),
            new App\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(App\ReadModel\RunViewInterface::class),
            ),
            new App\Middleware\FetchPublicationMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ),
            new App\Middleware\FetchDescriptionMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ),
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
