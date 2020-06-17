<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class NotFoundJsonBodyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $code = $response->getStatusCode();
        $body = $response->getBody();
        $accept = $request->getHeaderLine('accept');

        return $code == 404 && empty((string) $body) && strpos($accept, 'application/json') !== false
            ? $this->notFoundResponse($response)
            : $response;
    }

    public function notFoundResponse(ResponseInterface $response): ResponseInterface
    {
        $contents = json_encode(['code' => 404, 'success' => false, 'data' => []]);

        if ($contents === false) {
            throw new \Exception(json_last_error_msg());
        }

        $response->getBody()->write($contents);

        return $response->withHeader('content-type', 'application/json');
    }
}
