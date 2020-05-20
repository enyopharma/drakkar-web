<?php

declare(strict_types=1);

namespace App\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    public function __construct(HtmlResponder $responder, RunViewInterface $runs, AssociationViewInterface $associations)
    {
        $this->responder = $responder;
        $this->runs = $runs;
        $this->associations = $associations;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // parse request.
        $run_id = (int) $request->getAttribute('run_id');

        $params = (array) $request->getQueryParams();

        $state = (string) ($params['state'] ?? 'pending');
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the run.
        if (!$run = $this->runs->id($run_id, 'nbs')->fetch()) {
            return $this->responder->notFound();
        }

        // get the publications.
        $total = $run['nbs'][$state];
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $this->outOfRangeResponse($run, $state, 1, 20);
        }

        if ($page < 1) {
            return $this->outOfRangeResponse($run, $state, 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->outOfRangeResponse($run, $state, (int) ceil($total/$limit), $limit);
        }

        // success!
        return $this->responder->success('publications/index', [
            'run' => $run,
            'publications' => $this->associations->all($run_id, $state, $limit, $offset)->fetchAll(),
            'state' => $state,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    private function outOfRangeResponse(array $run, string $state, int $page, int $limit): ResponseInterface
    {
        $query = ['state' => $state, 'page' => $page, 'limit' => $limit];

        return $this->responder->temporary('runs.publications.index', $run, $query, 'publications');
    }
}
