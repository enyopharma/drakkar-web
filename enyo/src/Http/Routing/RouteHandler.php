<?php declare(strict_types=1);

namespace Enyo\Http\Routing;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RouteHandler implements MiddlewareInterface
{
    private $handler;

    public function __construct(RequestHandler $handler)
    {
        $this->handler = $handler;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        return $this->handler->handle($request);
    }
}
