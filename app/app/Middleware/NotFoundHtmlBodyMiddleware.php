<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use League\Plates\Engine;

final class NotFoundHtmlBodyMiddleware implements MiddlewareInterface
{
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $code = $response->getStatusCode();
        $body = $response->getBody();
        $accept = $request->getHeaderLine('accept');

        return $code == 404 && empty((string) $body) && strpos($accept, 'text/html') !== false
            ? $this->notFoundResponse($request, $response)
            : $response;
    }

    public function notFoundResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $contents = $this->engine->render('_errors/404', [
            'method' => $request->getMethod(),
            'url' => $request->getUri(),
        ]);

        $response->getBody()->write($contents);

        return $response->withHeader('content-type', 'text/html');
    }
}
