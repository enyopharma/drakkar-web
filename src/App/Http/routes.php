<?php

declare(strict_types=1);

use Enyo\Http\Routing\RouteCollector;

return function (RouteCollector $collector) {
    $collector->get('/', ...[
        App\Http\Handlers\Runs\IndexHandler::class,
        'runs.index',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications', ...[
        App\Http\Handlers\Publications\IndexHandler::class,
        'runs.publications.index',
    ]);

    $collector->put('/runs/{run_id:\d+}/publications/{pmid:\d+}', ...[
        App\Http\Handlers\Publications\UpdateHandler::class,
        'runs.publications.update',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions', ...[
        App\Http\Handlers\Descriptions\IndexHandler::class,
        'runs.publications.descriptions.index',
    ]);

    $collector->post('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions', ...[
        App\Http\Handlers\Descriptions\InsertHandler::class,
        'runs.publications.descriptions.store',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create', ...[
        App\Http\Handlers\Descriptions\CreateHandler::class,
        'runs.publications.descriptions.create',
    ]);

    $collector->get('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit', ...[
        App\Http\Handlers\Descriptions\EditHandler::class,
        'runs.publications.descriptions.edit',
    ]);

    $collector->delete('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}', ...[
        App\Http\Handlers\Descriptions\DeleteHandler::class,
        'runs.publications.descriptions.delete',
    ]);

    $collector->get('/methods', ...[
        App\Http\Handlers\Methods\IndexHandler::class,
        'methods.index',
    ]);

    $collector->get('/methods/{psimi_id}', ...[
        App\Http\Handlers\Methods\ShowHandler::class,
        'methods.show',
    ]);

    $collector->get('/proteins', ...[
        App\Http\Handlers\Proteins\IndexHandler::class,
        'proteins.index',
    ]);

    $collector->get('/proteins/{accession}', ...[
        App\Http\Handlers\Proteins\ShowHandler::class,
        'proteins.show',
    ]);

    $collector->get('/dataset', ...[
        App\Http\Handlers\Dataset\IndexHandler::class,
        'dataset',
    ]);

    $collector->post('/jobs/alignments', ...[
        App\Http\Handlers\Jobs\AlignmentHandler::class,
        'jobs.alignment',
    ]);
};
