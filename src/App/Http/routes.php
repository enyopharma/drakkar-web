<?php

declare(strict_types=1);

return function (Psr\Container\ContainerInterface $container, $collector) {

    // create the handlers factory.
    $handler = function (string $domain, string $responder) use ($container) {
        return new App\Http\Middleware\LazyMiddleware(function () use ($container, $domain, $responder) {
            return new App\Http\Middleware\HandlerMiddleware(
                new App\Http\Input\HttpInput,
                $container->get($domain),
                $container->get($responder)
            );
        });
    };

    $json = function (string $domain) use ($handler) {
        return $handler($domain, App\Http\Responders\JsonResponder::class);
    };

    $runs = function (string $domain) use ($handler) {
        return $handler($domain, App\Http\Responders\RunResponder::class);
    };

    $publications = function (string $domain) use ($handler) {
        return $handler($domain, App\Http\Responders\PublicationResponder::class);
    };

    $descriptions = function (string $domain) use ($handler) {
        return $handler($domain, App\Http\Responders\DescriptionResponder::class);
    };

    $dataset = function (string $domain) use ($handler) {
        return $handler($domain, App\Http\Responders\DatasetResponder::class);
    };

    // register the routes.
    $collector->get('/', ...[
        $runs(Domain\Actions\CollectRuns::class),
        'runs.index',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications', ...[
        $publications(Domain\Actions\CollectPublications::class),
        'runs.publications.index',
    ]);

    $collector->put('/runs/{run_id:\d+}/publications/{pmid:\d+}', ...[
        $publications(Domain\Actions\UpdatePublicationState::class),
        'runs.publications.update',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions', ...[
        $descriptions(Domain\Actions\CollectDescriptions::class),
        'runs.publications.descriptions.index',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create', ...[
        $descriptions(Domain\Actions\SelectPublication::class),
        'runs.publications.descriptions.create',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit', ...[
        $descriptions(Domain\Actions\SelectDescription::class),
        'runs.publications.descriptions.edit',
    ]);

    $collector->get('/dataset', ...[
        $dataset(Domain\Actions\CollectDataset::class),
        'dataset',
    ]);

    $collector->post('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions', ...[
        $json(Domain\Actions\CreateDescription::class),
    ]);

    $collector->delete('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}', ...[
        $json(Domain\Actions\DeleteDescription::class),
    ]);

    $collector->get('/methods', ...[
        $json(Domain\Actions\SearchMethods::class),
    ]);

    $collector->get('/methods/{psimi_id}', ...[
        $json(Domain\Actions\SelectMethod::class),
    ]);

    $collector->get('/proteins', ...[
        $json(Domain\Actions\SearchProteins::class),
    ]);

    $collector->get('/proteins/{accession}', ...[
        $json(Domain\Actions\SelectProtein::class),
    ]);

    $collector->post('/jobs/alignments', ...[
        $json(Domain\Actions\StartAlignment::class),
    ]);
};
