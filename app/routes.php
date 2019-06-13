<?php declare(strict_types=1);

use Enyo\Http\Routing\RouteCollector;

return function (RouteCollector $collector) {
    $collector->get('/', ...[
        App\Http\Handlers\Runs\IndexHandler::class,
        'index',
    ]);

    $collector->get('/runs/{id}', ...[
        App\Http\Handlers\Runs\ShowHandler::class,
        'runs.show',
    ]);

    $collector->put('/runs/{run_id}/publications/{pmid}', ...[
        App\Http\Handlers\Publications\UpdateHandler::class,
        'runs.publications',
    ]);

    $collector->post('/runs/{run_id}/publications/{pmid}/descriptions', ...[
        App\Http\Handlers\Descriptions\InsertHandler::class,
        'runs.publications.descriptions',
    ]);

    $collector->get('/runs/{run_id}/publications/{pmid}/descriptions/create', ...[
        App\Http\Handlers\Descriptions\CreateHandler::class,
        'runs.publications.descriptions.create',
    ]);

    $collector->get('/runs/{run_id}/publications/{pmid}/descriptions/{id}', ...[
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
