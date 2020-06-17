<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use Psr\Http\Message\ServerRequestInterface;

use League\Plates\Engine;

use App\Routing\UrlGenerator;
use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;

final class IndexEndpoint
{
    private Engine $engine;

    private UrlGenerator $generator;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    public function __construct(Engine $engine, UrlGenerator $generator, RunViewInterface $runs, AssociationViewInterface $associations)
    {
        $this->engine = $engine;
        $this->generator = $generator;
        $this->runs = $runs;
        $this->associations = $associations;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|string|false
     */
    public function __invoke(ServerRequestInterface $request, callable $responder)
    {
        // parse request.
        $run_id = (int) $request->getAttribute('run_id');

        $params = (array) $request->getQueryParams();

        $state = (string) ($params['state'] ?? 'pending');
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the run.
        if (!$run = $this->runs->id($run_id, 'nbs')->fetch()) {
            return false;
        }

        // get the publications.
        $total = $run['nbs'][$state];
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $responder(302, $this->outOfRangeUrl($run, $state, 1, 20));
        }

        if ($page < 1) {
            return $responder(302, $this->outOfRangeUrl($run, $state, 1, $limit));
        }

        if ($offset > 0 && $offset > $total) {
            return $responder(302, $this->outOfRangeUrl($run, $state, (int) ceil($total/$limit), $limit));
        }

        // success!
        return $this->engine->render('publications/index', [
            'run' => $run,
            'publications' => $this->associations->all($run_id, $state, $limit, $offset)->fetchAll(),
            'state' => $state,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    private function outOfRangeUrl(array $run, string $state, int $page, int $limit): string
    {
        $query = ['state' => $state, 'page' => $page, 'limit' => $limit];

        return $this->generator->generate('runs.publications.index', $run, $query, 'publications');
    }
}
