<?php

declare(strict_types=1);

namespace App\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\ProteinViewInterface;

use App\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private JsonResponder $responder;

    private ProteinViewInterface $proteins;

    public function __construct(JsonResponder $responder, ProteinViewInterface $proteins)
    {
        $this->responder = $responder;
        $this->proteins = $proteins;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getQueryParams();

        $type = (string) ($params['type'] ?? '');
        $query = (string) ($params['query'] ?? '');
        $limit = (int) ($params['limit'] ?? 5);

        $proteins = $this->proteins->search($type, $query, $limit)->fetchAll();

        return $this->responder->success($proteins);
    }
}
