<?php declare(strict_types=1);

use Enyo\Http\RouteCollector;

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

    $collector->get('/proteins', ...[
        App\Http\Handlers\Proteins\IndexHandler::class,
        'proteins.index',
    ]);

    $collector->get('/proteins/{accession}', ...[
        App\Http\Handlers\Proteins\ShowHandler::class,
        'proteins.show',
    ]);

    $collector->post('/runs/{run_id}/publications/{pmid}/descriptions', ...[
        App\Http\Handlers\Descriptions\InsertHandler::class,
        'runs.publications.descriptions',
    ]);
};
