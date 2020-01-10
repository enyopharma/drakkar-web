<?php

declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\MethodViewInterface;

use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $responder;

    private $methods;

    public function __construct(JsonResponder $responder, MethodViewInterface $methods)
    {
        $this->responder = $responder;
        $this->methods = $methods;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $psimi_id = (string) $request->getAttribute('psimi_id');

        return ($method = $this->methods->psimiId($psimi_id)->fetch())
            ? $this->responder->success($method)
            : $this->responder->notFound($request);
    }
}
