<?php declare(strict_types=1);

namespace Enyo\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HttpMethodMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        $body = (array) $request->getParsedBody() ?? [];

        if (in_array($body['_method'] ?? '', ['PUT', 'DELETE'])) {
            $request = $request->withMethod($body['_method']);
        }

        return $handler->handle($request);
    }
}
