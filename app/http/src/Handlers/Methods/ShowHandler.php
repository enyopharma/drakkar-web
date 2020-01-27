<?php

declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\MethodInterface;

use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $responder;

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
