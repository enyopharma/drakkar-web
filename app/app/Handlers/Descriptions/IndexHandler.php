<?php

declare(strict_types=1);

namespace App\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunInterface;
use App\ReadModel\PublicationInterface;

use App\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    public function __construct(HtmlResponder $responder)
    {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get parent run.
        $run = $request->getAttribute(RunInterface::class);

        if (! $run instanceof RunInterface) {
            throw new \LogicException;
        }

        // get parent publication.
        $publication = $request->getAttribute(PublicationInterface::class);

        if (! $publication instanceof PublicationInterface) {
            throw new \LogicException;
        }

        // get input.
        $params = (array) $request->getQueryParams();

        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the descriptions.
        $descriptions = $publication->descriptions();

        $offset = ($page - 1) * $limit;
        $total = $descriptions->count();

        if ($limit < 0) {
            return $this->outOfRangeResponse($publication, 1, 20);
        }

        if ($page < 1) {
            return $this->outOfRangeResponse($publication, 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->outOfRangeResponse($publication, (int) ceil($total/$limit), $limit);
        }

        // success!
        return $this->responder->success('descriptions/index', [
            'run' => $run->data(),
            'publication' => $publication->data(),
            'descriptions' => $descriptions->all($limit, $offset)->fetchAll(),
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    private function outOfRangeResponse(PublicationInterface $publication, int $page, int $limit): ResponseInterface
    {
        $data = $publication->data();

        $query = ['page' => $page, 'limit' => $limit];

        return $this->responder->temporary('runs.publications.descriptions.index', $data['url'], $query, 'descriptions');
    }
}
