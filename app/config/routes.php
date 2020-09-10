<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use App\Endpoints\Runs;
use App\Endpoints\Dataset;
use App\Endpoints\Methods;
use App\Endpoints\Proteins;
use App\Endpoints\Alignments;
use App\Endpoints\Publications;
use App\Endpoints\Descriptions;

/**
 * Return the route definitions.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array[]
 */
return function (ContainerInterface $container): array {
    $responder = new Quanta\Http\Responder(
        $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
    );

    $lazy = fn (callable $f) => new Quanta\Http\LazyRequestHandler($f);
    $endpoint = fn (callable $f) => new Quanta\Http\Endpoint($responder, $f);

    return array_map($lazy, [
        'GET /' => fn () => $endpoint(new Runs\IndexEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\RunViewInterface::class),
        )),

        'GET /publications' => fn () => $endpoint(new Publications\SearchEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\PublicationViewInterface::class),
        )),

        'GET /descriptions' => fn () => $endpoint(new Descriptions\SearchEndpoint(
            $container->get(App\Routing\UrlGenerator::class),
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\DescriptionViewInterface::class),
        )),

        'GET /runs/{run_id:\d+}/publications' => fn () => $endpoint(new Publications\IndexEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\Routing\UrlGenerator::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
        )),

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => fn () => $endpoint(new Publications\UpdateEndpoint(
            $container->get(App\Actions\UpdatePublicationStateInterface::class),
        )),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => $endpoint(new Descriptions\IndexEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\Routing\UrlGenerator::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
            $container->get(App\ReadModel\DescriptionViewInterface::class),
        )),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => fn () => $endpoint(new Descriptions\CreateEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
        )),

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/{type:copy|edit}' => fn () => $endpoint(new Descriptions\EditEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\RunViewInterface::class),
            $container->get(App\ReadModel\AssociationViewInterface::class),
            $container->get(App\ReadModel\FormViewInterface::class),
        )),

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => fn () => Quanta\Http\RequestHandler::queue(
            $endpoint(new Descriptions\StoreEndpoint(
                $container->get(App\Actions\StoreDescriptionInterface::class),
            )),
            new App\Middleware\ValidateDescriptionMiddleware(
                $container->get(PDO::class),
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ),
        ),

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => fn () => $endpoint(new Descriptions\DeleteEndpoint(
            $container->get(App\Actions\DeleteDescriptionInterface::class),
        )),

        'GET /methods' => fn () => $endpoint(new Methods\IndexEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        )),

        'GET /methods/{id:[0-9]+}' => fn () => $endpoint(new Methods\ShowEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        )),

        'GET /proteins' => fn () => $endpoint(new Proteins\IndexEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        )),

        'GET /proteins/{id:[0-9]+}' => fn () => $endpoint(new Proteins\ShowEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        )),

        'GET /dataset/{type:hh|vh}' => fn () => $endpoint(new Dataset\DownloadEndpoint(
            $container->get(App\ReadModel\DatasetViewInterface::class),
        )),

        'POST /jobs/alignments' => fn () => $endpoint(new Alignments\StartEndpoint(
            $container->get(Predis\Client::class),
        )),
    ]);
};
