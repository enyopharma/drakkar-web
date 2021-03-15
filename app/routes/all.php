<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use App\Routing\Route;

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
    $factory = $container->get(Psr\Http\Message\ResponseFactoryInterface::class);

    $serializer = new Quanta\Http\MetadataSerializer('data', ['success' => true, 'code' => 200]);

    $endpoint = fn (callable $f) => new Quanta\Http\Endpoint($factory, $f, $serializer);

    return [
        Route::named('runs.index')->get('/')->handler(fn () => $endpoint(new Runs\IndexEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\RunViewInterface::class),
        ))),

        Route::named('publications.index')->get('/publications')
            ->handler(fn () => $endpoint(new Publications\SearchEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\PublicationViewInterface::class),
            ))),

        Route::named('descriptions.index')->get('/descriptions')
            ->handler(fn () => $endpoint(new Descriptions\SearchEndpoint(
                $container->get(App\Routing\UrlGenerator::class),
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\DescriptionViewInterface::class),
            ))),

        Route::named('runs.publications.index')
            ->get('/runs/{id:\d+}/publications')
            ->handler(fn () => $endpoint(new Publications\IndexEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\Routing\UrlGenerator::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            ))),

        Route::named('runs.publications.update')
            ->put('/runs/{run_id:\d+}/publications/{pmid:\d+}')
            ->handler(fn () => $endpoint(new Publications\UpdateEndpoint(
                $container->get(App\Actions\UpdatePublicationStateInterface::class),
            ))),

        Route::named('runs.publications.descriptions.index')
            ->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')
            ->handler(fn () => $endpoint(new Descriptions\IndexEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\Routing\UrlGenerator::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\DescriptionViewInterface::class),
            ))),

        Route::named('runs.publications.descriptions.create')
            ->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create')
            ->handler(fn () => $endpoint(new Descriptions\CreateEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            ))),

        Route::named('runs.publications.descriptions.edit')
            ->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/{type:copy|edit}')
            ->handler(fn () => $endpoint(new Descriptions\EditEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\FormViewInterface::class),
            ))),

        Route::named('dataset')
            ->get('/dataset/{type:hh|vh}')
            ->handler(fn () => $endpoint(new Dataset\DownloadEndpoint(
                $container->get(App\ReadModel\DatasetViewInterface::class),
            ))),

        Route::get('/methods')->handler(fn () => $endpoint(new Methods\IndexEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        ))),

        Route::get('/methods/{id:[0-9]+}')->handler(fn () => $endpoint(new Methods\ShowEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        ))),

        Route::get('/proteins')->handler(fn () => $endpoint(new Proteins\IndexEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ))),

        Route::get('/proteins/{id:[0-9]+}')->handler(fn () => $endpoint(new Proteins\ShowEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ))),

        Route::post('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')
            ->handler(fn () => $endpoint(new Descriptions\StoreEndpoint(
                $container->get(App\Actions\StoreDescriptionInterface::class),
            )))
            ->middleware(fn () => new App\Middleware\ValidateDescriptionMiddleware(
                $container->get(PDO::class),
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            )),

        Route::delete('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}')
            ->handler(fn () => $endpoint(new Descriptions\DeleteEndpoint(
                $container->get(App\Actions\DeleteDescriptionInterface::class),
            ))),

        Route::post('/jobs/alignments')->handler(fn () => $endpoint(new Alignments\StartEndpoint(
            $container->get(Predis\Client::class),
        ))),
    ];
};
