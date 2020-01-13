<?php

declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;

use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $responder;

    private $runs;

    private $publications;

    public function __construct(HtmlResponder $responder, RunViewInterface $runs, PublicationViewInterface $publications)
    {
        $this->responder = $responder;
        $this->runs = $runs;
        $this->publications = $publications;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get input.
        $run_id = (int) $request->getAttribute('run_id');

        $params = (array) $request->getQueryParams();

        $state = (string) ($params['state'] ?? \Domain\Publication::PENDING);
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the run.
        $select_run_sth = $this->runs->id($run_id);

        if (! $run = $select_run_sth->fetch()) {
            return $this->responder->notFound($request);
        }

        // get the publications.
        $offset = ($page - 1) * $limit;
        $total = $this->publications->count($run['id'], $state);

        if ($limit < 0) {
            return $this->outOfRangeResponse($run_id, $state, 1, 20);
        }

        if ($page < 1) {
            return $this->outOfRangeResponse($run_id, $state, 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->outOfRangeResponse($run_id, $state, (int) ceil($total/$limit), $limit);
        }

        $publications = $this->publications->all($run_id, $state, $limit, $offset)->fetchAll();

        // success!
        return $this->responder->success('publications/index', [
            'run' => $run,
            'publications' => $publications,
            'state' => $state,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    private function outOfRangeResponse(int $run_id, string $state, int $page, int $limit): ResponseInterface
    {
        $params = ['run_id' => $run_id];

        $query = ['state' => $state, 'page' => $page, 'limit' => $limit];

        return $this->responder->route('runs.publications.index', $params, $query, 'publications');
    }
}
