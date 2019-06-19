<?php declare(strict_types=1);

use Enyo\Http\Routing\RouteCollector;

return function (RouteCollector $collector) {
    $collector->get('/', ...[
        App\Http\Handlers\Runs\IndexHandler::class,
        'runs.index',
    ]);

    $collector->get('/runs/{id}', ...[
        App\Http\Handlers\Runs\ShowHandler::class,
        'runs.show',
    ]);

    $collector->get('/runs/{run_id}/publications/{pmid}', ...[
        App\Http\Handlers\Publications\ShowHandler::class,
        'runs.publications.show',
    ]);

    $collector->put('/runs/{run_id}/publications/{pmid}', ...[
        App\Http\Handlers\Publications\UpdateHandler::class,
        'runs.publications.update',
    ]);

    $collector->post('/runs/{run_id}/publications/{pmid}/descriptions', ...[
        App\Http\Handlers\Descriptions\InsertHandler::class,
        'runs.publications.descriptions.store',
    ]);

    $collector->get('/runs/{run_id}/publications/{pmid}/descriptions/create', ...[
        App\Http\Handlers\Descriptions\CreateHandler::class,
        'runs.publications.descriptions.create',
    ]);

    $collector->get('/runs/{run_id}/publications/{pmid}/descriptions/{id}/edit', ...[
        App\Http\Handlers\Descriptions\EditHandler::class,
        'runs.publications.descriptions.edit',
    ]);

    $collector->get('/methods', ...[
        App\Http\Handlers\Methods\IndexHandler::class,
        'methods.index',
    ]);

    $collector->get('/proteins', ...[
        App\Http\Handlers\Proteins\IndexHandler::class,
        'proteins.index',
    ]);

    $collector->get('/proteins/{accession}', ...[
        App\Http\Handlers\Proteins\ShowHandler::class,
        'proteins.show',
    ]);

    $collector->post('/jobs/alignments', ...[
        App\Http\Handlers\Jobs\AlignmentHandler::class,
        'jobs.alignment',
    ]);
};
