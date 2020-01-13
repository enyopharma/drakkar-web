<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use App\Http\Handlers\LazyRequestHandler;

/**
 * Return the route definitions.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array[]
 */
return function (ContainerInterface $container): array {
    return [
        'GET /' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Runs\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class)
            );
        }),

        'GET /runs/{run_id:\d+}/publications' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Publications\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );
        }),

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Publications\UpdateHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\Actions\UpdatePublicationStateInterface::class)
            );
        }),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\IndexHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class),
                $container->get(Domain\ReadModel\DescriptionViewInterface::class)
            );
        }),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\CreateHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );
        }),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\EditHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\RunViewInterface::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class),
                $container->get(Domain\ReadModel\DescriptionViewInterface::class)
            );
        }),

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\StoreHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\Actions\StoreDescriptionInterface::class)
            );
        }),

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Descriptions\DeleteHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\Actions\DeleteDescriptionInterface::class)
            );
        }),

        'GET /publications' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Publications\SearchHandler(
                $container->get(App\Http\Responders\HtmlResponder::class),
                $container->get(Domain\ReadModel\PublicationViewInterface::class)
            );
        }),

        'GET /dataset/{type:hh|vh}' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Dataset\DownloadHandler(
                $container->get(App\Http\Responders\FileResponder::class),
                $container->get(Domain\ReadModel\DatasetViewInterface::class)
            );
        }),

        'GET /methods' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Methods\IndexHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\MethodViewInterface::class)
            );
        }),

        'GET /methods/{psimi_id}' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Methods\ShowHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\MethodViewInterface::class)
            );
        }),

        'GET /proteins' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Proteins\IndexHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\ProteinViewInterface::class)
            );
        }),

        'GET /proteins/{accession}' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Proteins\ShowHandler(
                $container->get(App\Http\Responders\JsonResponder::class),
                $container->get(Domain\ReadModel\ProteinViewInterface::class)
            );
        }),

        'POST /jobs/alignments' => new LazyRequestHandler(function () use ($container) {
            return new App\Http\Handlers\Alignments\StartHandler(
                $container->get(Predis\Client::class),
                $container->get(App\Http\Responders\JsonResponder::class)
            );
        }),
    ];
};
