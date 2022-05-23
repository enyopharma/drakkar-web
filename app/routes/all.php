<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Http\Route;
use Quanta\Http\Endpoint;
use Quanta\Http\MetadataSerializer;

use App\Endpoints\Runs;
use App\Endpoints\Taxa;
use App\Endpoints\Dataset;
use App\Endpoints\Methods;
use App\Endpoints\Proteins;
use App\Endpoints\Peptides;
use App\Endpoints\Alignments;
use App\Endpoints\Publications;
use App\Endpoints\Descriptions;

/**
 * Return the route definitions.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array<int, Quanta\Http\Route>
 */
return function (ContainerInterface $container): array {
    $factory = $container->get(ResponseFactoryInterface::class);

    $serializer = new MetadataSerializer('data', ['success' => true, 'code' => 200]);

    $endpoint = fn (callable $f) => new Endpoint($factory, $f, $serializer);

    return [
        Route::named('runs.index')->matching('/')->get(fn () => $endpoint(new Runs\IndexEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\RunViewInterface::class),
        ))),

        Route::named('publications.index')
            ->matching('/publications')
            ->get(fn () => $endpoint(new Publications\SearchEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\PublicationViewInterface::class),
            ))),

        Route::named('descriptions.index')
            ->matching('/descriptions')
            ->get(fn () => $endpoint(new Descriptions\SearchEndpoint(
                $container->get(Quanta\Http\UrlGenerator::class),
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\DescriptionViewInterface::class),
            ))),

        Route::named('runs.publications.index')
            ->matching('/runs/{id:\d+}/publications')
            ->get(fn () => $endpoint(new Publications\IndexEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(Quanta\Http\UrlGenerator::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            ))),

        Route::named('runs.publications.update')
            ->matching('/runs/{run_id:\d+}/publications/{pmid:\d+}')
            ->put(fn () => $endpoint(new Publications\UpdateEndpoint(
                $container->get(App\Actions\UpdatePublicationStateInterface::class),
            ))),

        Route::named('runs.publications.descriptions.index')
            ->matching('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')
            ->get(fn () => $endpoint(new Descriptions\IndexEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(Quanta\Http\UrlGenerator::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\DescriptionViewInterface::class),
            ))),

        Route::named('runs.publications.descriptions.create')
            ->matching('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create')
            ->get(fn () => $endpoint(new Descriptions\CreateEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            ))),

        Route::named('runs.publications.descriptions.edit')
            ->matching('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/{type:copy|edit}')
            ->get(fn () => $endpoint(new Descriptions\EditEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\FormViewInterface::class),
            ))),

        Route::named('runs.publications.descriptions.peptides.index')
            ->matching('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/peptides')
            ->get(fn () => $endpoint(new Peptides\IndexEndpoint(
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\FormViewInterface::class),
                $container->get(App\ReadModel\PeptideViewInterface::class),
            ))),

        Route::named('dataset')
            ->matching('/dataset/{type:hh|vh}')
            ->get(fn () => $endpoint(new Dataset\DownloadEndpoint(
                $container->get(App\ReadModel\DatasetViewInterface::class),
            ))),

        Route::matching('/methods')->get(fn () => $endpoint(new Methods\IndexEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        ))),

        Route::matching('/methods/{id:[0-9]+}')->get(fn () => $endpoint(new Methods\ShowEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        ))),

        Route::matching('/proteins')->get(fn () => $endpoint(new Proteins\IndexEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ))),

        Route::matching('/proteins/{id:[0-9]+}')->get(fn () => $endpoint(new Proteins\ShowEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ))),

        Route::matching('/taxa/{ncbi_taxon_id:[0-9]+}/names')->get(fn () => $endpoint(new Taxa\ShowEndpoint(
            $container->get(App\ReadModel\TaxonViewInterface::class),
        ))),

        Route::matching('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')
            ->middleware(fn () => new App\Middleware\ValidateDescriptionMiddleware(
                $container->get(PDO::class),
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ))
            ->post(fn () => $endpoint(new Descriptions\StoreEndpoint(
                $container->get(App\Actions\StoreDescriptionInterface::class),
            ))),

        Route::matching('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}')
            ->delete(fn () => $endpoint(new Descriptions\DeleteEndpoint(
                $container->get(App\Actions\DeleteDescriptionInterface::class),
            ))),

        Route::matching('/jobs/alignments')->post(fn () => $endpoint(new Alignments\StartEndpoint(
            $container->get(Predis\Client::class),
        ))),
    ];
};
