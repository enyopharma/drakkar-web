<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HttpMethodMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = (array) $request->getParsedBody() ?? [];

        if (in_array($body['_method'] ?? '', ['PUT', 'DELETE'])) {
            $request = $request->withMethod($body['_method']);
        }

        return $handler->handle($request);
    }
}
