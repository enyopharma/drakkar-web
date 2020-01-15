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
    /**
     * Factory producing request handler.
     */
    $handler = function (callable $factory) {
        return Quanta\Http\RequestHandler::queue(
            new App\Http\Handlers\LazyRequestHandler($factory),
            new Middlewares\JsonPayload
        );
    };

    /**
     * The route definitions.
     */
    return [
        'GET /' => $handler(function () use ($container) {
            return new App\Http\Handlers\Runs\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );
        }),

        'GET /runs/{run_id:\d+}/publications' => $handler(function () use ($container) {
            $run = new App\Http\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );

            $handler = new App\Http\Handlers\Publications\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );

            return Quanta\Http\RequestHandler::queue($handler, $run);
        }),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => $handler(function () use ($container) {
            $run = new App\Http\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );

            $publication = new App\Http\Middleware\FetchPublicationMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );

            $handler =  new App\Http\Handlers\Descriptions\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\DescriptionViewInterface::class)
            );

            return Quanta\Http\RequestHandler::queue($handler, $run, $publication);
        }),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => $handler(function () use ($container) {
            $run = new App\Http\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );

            $publication = new App\Http\Middleware\FetchPublicationMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );

            $handler =  new App\Http\Handlers\Descriptions\CreateHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
            );

            return Quanta\Http\RequestHandler::queue($handler, $run, $publication);
        }),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => $handler(function () use ($container) {
            $run = new App\Http\Middleware\FetchRunMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );

            $publication = new App\Http\Middleware\FetchPublicationMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );

            $description = new App\Http\Middleware\FetchDescriptionMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\DescriptionViewInterface::class)
            );

            $handler =  new App\Http\Handlers\Descriptions\EditHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
            );

            return Quanta\Http\RequestHandler::queue($handler, $run, $publication, $description);
        }),

        'GET /methods/{psimi_id}' => $handler(function () use ($container) {
            $method = new App\Http\Middleware\FetchMethodMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\MethodViewInterface::class)
            );

            $handler = new App\Http\Handlers\Methods\ShowHandler(
                $container->get(App\Http\Responders\JsonResponder::class)
            );

            return Quanta\Http\RequestHandler::queue($handler, $method);
        }),

        'GET /proteins/{accession}' => $handler(function () use ($container) {
            $protein = new App\Http\Middleware\FetchProteinMiddleware(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
                $container->get(Domain\ReadModel\ProteinViewInterface::class)
            );

            $handler = new App\Http\Handlers\Proteins\ShowHandler(
                $container->get(App\Http\Responders\JsonResponder::class)
            );

            return Quanta\Http\RequestHandler::queue($handler, $protein);
        }),

        'GET /methods' => $handler(function () use ($container) {
            return new App\Http\Handlers\Methods\IndexHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\MethodViewInterface::class)
            );
        }),

        'GET /proteins' => $handler(function () use ($container) {
            return new App\Http\Handlers\Proteins\IndexHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\ProteinViewInterface::class)
            );
        }),

        'GET /publications' => $handler(function () use ($container) {
            return new App\Http\Handlers\Publications\SearchHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );
        }),

        'GET /dataset/{type:hh|vh}' => $handler(function () use ($container) {
            return new App\Http\Handlers\Dataset\DownloadHandler(
                $container->get(App\Http\Responders\FileResponder::class),
                $container->get(Domain\ReadModel\DatasetViewInterface::class)
            );
        }),

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => $handler(function () use ($container) {
            return new App\Http\Handlers\Publications\UpdateHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\Actions\UpdatePublicationStateInterface::class)
            );
        }),

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => $handler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\StoreHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\Actions\StoreDescriptionInterface::class)
            );
        }),

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => $handler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\DeleteHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\Actions\DeleteDescriptionInterface::class)
            );
        }),

        'POST /jobs/alignments' => $handler(function () use ($container) {
            return new App\Http\Handlers\Alignments\StartHandler(
                $container->get(Predis\Client::class),
                $container->get(App\Http\Responders\JsonResponder::class)
            );
        }),
    ];
};
