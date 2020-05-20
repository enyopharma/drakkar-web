<?php

declare(strict_types=1);

namespace App\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodInterface;

use App\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private JsonResponder $responder;

    public function __construct(JsonResponder $responder)
    {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getAttribute(MethodInterface::class);

        if (! $method instanceof MethodInterface) {
            throw new \LogicException;
        }

        return $this->responder->success($method->data());
    }
}
