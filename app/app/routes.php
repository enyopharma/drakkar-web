<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Quanta\Http\RequestHandler;

use App\Handlers\Runs;
use App\Handlers\Dataset;
use App\Handlers\Methods;
use App\Handlers\Proteins;
use App\Handlers\Alignments;
use App\Handlers\Publications;
use App\Handlers\Descriptions;
use App\Handlers\LazyRequestHandler;

/**
 * Return the route definitions.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array[]
 */
return function (ContainerInterface $container): array {
    $lazy = fn (callable $f) => new LazyRequestHandler($f);

    return array_map($lazy, [
        'GET /' => fn () => new Runs\IndexHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
        ),

        'GET /publications' => fn () => new Publications\SearchHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\PublicationViewInterface::class),
        ),

        'GET /runs/{run_id:\d+}/publications' => fn () => new Publications\IndexHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
        ),

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => fn () => new Publications\UpdateHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\Actions\UpdatePublicationStateInterface::class),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => new Descriptions\IndexHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
            $container->get(App\ReadModel\DescriptionViewInterface::class),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => fn () => new Descriptions\CreateHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
        ),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => fn () => new Descriptions\EditHandler(
            $container->get(App\Responders\HtmlResponder::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
            $container->get(App\ReadModel\DescriptionViewInterface::class),
        ),

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => RequestHandler::queue(
            new Descriptions\StoreHandler(
                $container->get(App\Responders\JsonResponder::class),
                $container->get(App\Actions\StoreDescriptionInterface::class),
            ),
            new App\Middleware\ValidateDescriptionMiddleware(
                $container->get(App\Responders\JsonResponder::class),
                $container->get(PDO::class),
            ),
        ),

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => fn () => new Descriptions\DeleteHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\Actions\DeleteDescriptionInterface::class),
        ),

        'GET /methods' => fn () => new Methods\IndexHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\MethodViewInterface::class),
        ),

        'GET /methods/{psimi_id}' => fn () => new Methods\ShowHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\MethodViewInterface::class),
        ),

        'GET /proteins' => fn () => new Proteins\IndexHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ),

        'GET /proteins/{accession}' => fn () => new Proteins\ShowHandler(
            $container->get(App\Responders\JsonResponder::class),
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ),

        'GET /dataset/{type:hh|vh}' => fn () => new Dataset\DownloadHandler(
            $container->get(App\Responders\FileResponder::class),
            $container->get(App\ReadModel\DatasetViewInterface::class),
        ),

        'POST /jobs/alignments' => fn () => new Alignments\StartHandler(
            $container->get(Predis\Client::class),
            $container->get(App\Responders\JsonResponder::class),
        ),
    ]);
};
