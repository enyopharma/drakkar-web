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

    $collector->put('/runs/{run_id}/publications/{id}', ...[
        App\Http\Handlers\Publications\UpdateHandler::class,
        'runs.publications.update',
    ]);
};
