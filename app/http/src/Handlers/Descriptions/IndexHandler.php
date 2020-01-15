<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\DescriptionViewInterface;

use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $responder;

    private $descriptions;

    public function __construct(HtmlResponder $responder, DescriptionViewInterface $descriptions)
    {
        $this->responder = $responder;
        $this->descriptions = $descriptions;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get input.
        $run = $request->getAttribute('run');
        $publication = $request->getAttribute('publication');

        $params = (array) $request->getQueryParams();

        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the descriptions.
        if (is_null($run) || is_null($publication)) throw new \LogicException;

        $offset = ($page - 1) * $limit;
        $total = $this->descriptions->count($run['id'], $publication['pmid']);

        if ($limit < 0) {
            return $this->outOfRangeResponse($run['id'], $publication['pmid'], 1, 20);
        }

        if ($page < 1) {
            return $this->outOfRangeResponse($run['id'], $publication['pmid'], 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->outOfRangeResponse($run['id'], $publication['pmid'], (int) ceil($total/$limit), $limit);
        }

        $descriptions = $this->descriptions
            ->all($run['id'], $publication['pmid'], $limit, $offset)
            ->fetchAll();

        // success!
        return $this->responder->success('descriptions/index', [
            'run' => $run,
            'publication' => $publication,
            'descriptions' => $descriptions,
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    private function outOfRangeResponse(int $run_id, int $pmid, int $page, int $limit): ResponseInterface
    {
        $params = ['run_id' => $run_id, 'pmid' => $pmid];

        $query = ['page' => $page, 'limit' => $limit];

        return $this->responder->temporary('runs.publications.descriptions.index', $params, $query, 'descriptions');
    }
}
