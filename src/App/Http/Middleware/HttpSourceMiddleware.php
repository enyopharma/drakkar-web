<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HttpSourceMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        $body = (array) $request->getParsedBody() ?? [];

        $request = $request->withAttribute('source', $body['_source'] ?? '');

        return $handler->handle($request);
    }
}
