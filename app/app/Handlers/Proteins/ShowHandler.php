<?php

declare(strict_types=1);

namespace App\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\ProteinInterface;

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
        $protein = $request->getAttribute(ProteinInterface::class);

        if (! $protein instanceof ProteinInterface) {
            throw new \LogicException;
        }

        $protein = $protein
            ->withIsoforms()
            ->withDomains()
            ->withChains()
            ->withMatures();

        return $this->responder->success($protein->data());
    }
}
