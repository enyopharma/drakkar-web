<?php

declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\ProteinInterface;

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
