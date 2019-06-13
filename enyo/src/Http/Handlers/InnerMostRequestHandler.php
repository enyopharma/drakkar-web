<?php declare(strict_types=1);

namespace Enyo\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class InnerMostRequestHandler implements RequestHandlerInterface
{
    public function handle(Request $request): Response
    {
        throw new \LogicException(
            vsprintf('No middleware returned a response for the request %s %s', [
                strtoupper($request->getMethod()),
                $request->getUri(),
            ])
        );
    }
}
