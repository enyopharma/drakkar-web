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

    $collector->delete('/runs/{run_id}/publications/{pmid}/descriptions/{id}', ...[
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

    $collector->get('/proteins/{accession}/isoforms', ...[
        App\Http\Handlers\Proteins\Isoforms\IndexHandler::class,
        'proteins.isoforms.index',
    ]);

    $collector->get('/proteins/{accession}/matures', ...[
        App\Http\Handlers\Proteins\Matures\IndexHandler::class,
        'proteins.matures.index',
    ]);

    $collector->get('/proteins/{accession}/domains', ...[
        App\Http\Handlers\Proteins\Domains\IndexHandler::class,
        'proteins.domains.index',
    ]);

    $collector->get('/proteins/{accession}/chains', ...[
        App\Http\Handlers\Proteins\Chains\IndexHandler::class,
        'proteins.chains.index',
    ]);

    $collector->post('/jobs/alignments', ...[
        App\Http\Handlers\Jobs\AlignmentHandler::class,
        'jobs.alignment',
    ]);
};
