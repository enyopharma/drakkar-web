<?php

declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $protein = $request->getAttribute('protein');

        if (is_null($protein)) throw new \LogicException;

        return $this->responder->success($protein);
    }
}
