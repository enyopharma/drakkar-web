<?php declare(strict_types=1);

use Enyo\Http\RouteCollector;

return function (RouteCollector $collector) {
    $collector->get('/', ...[
        Http\Handlers\IndexHandler::class,
        'index',
    ]);

    $collector->get('/runs/{id}', ...[
        Http\Handlers\Runs\ShowHandler::class,
        'runs.show',
    ]);

    $collector->put('/runs/{run_id}/publications/{id}', ...[
        Http\Handlers\Publications\UpdateHandler::class,
        'runs.publications.update',
    ]);
};
