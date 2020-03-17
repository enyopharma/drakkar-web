<?php

declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunInterface;

use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $responder;

    public function __construct(HtmlResponder $responder)
    {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get the parent run.
        $run = $request->getAttribute(RunInterface::class);

        if (! $run instanceof RunInterface) {
            throw new \LogicException;
        }

        // get input.
        $params = (array) $request->getQueryParams();

        $state = (string) ($params['state'] ?? \Domain\Publication::PENDING);
        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the publications.
        $publications = $run->publications();

        $offset = ($page - 1) * $limit;
        $total = $publications->count($state);

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
            'run' => $run->withNbPublications()->data(),
            'publications' => $publications->all($state, $limit, $offset)->fetchAll(),
            'state' => $state,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    private function outOfRangeResponse(RunInterface $run, string $state, int $page, int $limit): ResponseInterface
    {
        $data = $run->data();

        $query = ['state' => $state, 'page' => $page, 'limit' => $limit];

        return $this->responder->temporary('runs.publications.index', $data['url'], $query, 'publications');
    }
}
