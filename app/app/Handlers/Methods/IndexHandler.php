<?php

declare(strict_types=1);

namespace App\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodViewInterface;

use App\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private JsonResponder $responder;

    private MethodViewInterface $methods;

    public function __construct(JsonResponder $responder, MethodViewInterface $methods)
    {
        $this->responder = $responder;
        $this->methods = $methods;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getQueryParams();

        $query = (string) ($params['query'] ?? '');
        $limit = (int) ($params['limit'] ?? 5);

        $methods = $this->methods->search($query, $limit)->fetchAll();

        return $this->responder->success($methods);
    }
}
