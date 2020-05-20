<?php

declare(strict_types=1);

namespace App\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodViewInterface;
use App\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
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
        $psimi_id = $request->getAttribute('psimi_id');

        $method = $this->methods->psimiId($psimi_id)->fetch();

        return $method
            ? $this->responder->success($method)
            : $this->responder->notFound();
    }
}
