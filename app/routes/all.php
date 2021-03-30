<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Http\Endpoint;
use Quanta\Http\RouteFactory;
use Quanta\Http\MetadataSerializer;

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
 * @return array<int, Quanta\Http\Route>
 */
return function (ContainerInterface $container): array {
    $root = RouteFactory::root();

    $factory = $container->get(ResponseFactoryInterface::class);

    $serializer = new MetadataSerializer('data', ['success' => true, 'code' => 200]);

    $endpoint = fn (callable $f) => new Endpoint($factory, $f, $serializer);

    return [
        $root->name('runs.index')->pattern('/')->get(fn () => $endpoint(new Runs\IndexEndpoint(
            $container->get(League\Plates\Engine::class),
            $container->get(App\ReadModel\RunViewInterface::class),
        ))),

        $root->name('publications.index')
            ->pattern('/publications')
            ->get(fn () => $endpoint(new Publications\SearchEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\PublicationViewInterface::class),
            ))),

        $root->name('descriptions.index')
            ->pattern('/descriptions')
            ->get(fn () => $endpoint(new Descriptions\SearchEndpoint(
                $container->get(Quanta\Http\UrlGenerator::class),
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\DescriptionViewInterface::class),
            ))),

        $root->name('runs.publications.index')
            ->pattern('/runs/{id:\d+}/publications')
            ->get(fn () => $endpoint(new Publications\IndexEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(Quanta\Http\UrlGenerator::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            ))),

        $root->name('runs.publications.update')
            ->pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}')
            ->put(fn () => $endpoint(new Publications\UpdateEndpoint(
                $container->get(App\Actions\UpdatePublicationStateInterface::class),
            ))),

        $root->name('runs.publications.descriptions.index')
            ->pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')
            ->get(fn () => $endpoint(new Descriptions\IndexEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(Quanta\Http\UrlGenerator::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\DescriptionViewInterface::class),
            ))),

        $root->name('runs.publications.descriptions.create')
            ->pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create')
            ->get(fn () => $endpoint(new Descriptions\CreateEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
            ))),

        $root->name('runs.publications.descriptions.edit')
            ->pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/{type:copy|edit}')
            ->get(fn () => $endpoint(new Descriptions\EditEndpoint(
                $container->get(League\Plates\Engine::class),
                $container->get(App\ReadModel\RunViewInterface::class),
                $container->get(App\ReadModel\AssociationViewInterface::class),
                $container->get(App\ReadModel\FormViewInterface::class),
            ))),

        $root->name('dataset')
            ->pattern('/dataset/{type:hh|vh}')
            ->get(fn () => $endpoint(new Dataset\DownloadEndpoint(
                $container->get(App\ReadModel\DatasetViewInterface::class),
            ))),

        $root->pattern('/methods')->get(fn () => $endpoint(new Methods\IndexEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        ))),

        $root->pattern('/methods/{id:[0-9]+}')->get(fn () => $endpoint(new Methods\ShowEndpoint(
            $container->get(App\ReadModel\MethodViewInterface::class),
        ))),

        $root->pattern('/proteins')->get(fn () => $endpoint(new Proteins\IndexEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ))),

        $root->pattern('/proteins/{id:[0-9]+}')->get(fn () => $endpoint(new Proteins\ShowEndpoint(
            $container->get(App\ReadModel\ProteinViewInterface::class),
        ))),

        $root->pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')
            ->middleware(fn () => new App\Middleware\ValidateDescriptionMiddleware(
                $container->get(PDO::class),
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            ))
            ->post(fn () => $endpoint(new Descriptions\StoreEndpoint(
                $container->get(App\Actions\StoreDescriptionInterface::class),
            ))),

        $root->pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}')
            ->delete(fn () => $endpoint(new Descriptions\DeleteEndpoint(
                $container->get(App\Actions\DeleteDescriptionInterface::class),
            ))),

        $root->pattern('/jobs/alignments')->post(fn () => $endpoint(new Alignments\StartEndpoint(
            $container->get(Predis\Client::class),
        ))),
    ];
};
