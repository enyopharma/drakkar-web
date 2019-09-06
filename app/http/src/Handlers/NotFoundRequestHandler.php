<?php

declare(strict_types=1);

namespace App\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

final class NotFoundRequestHandler implements RequestHandlerInterface
{
    private $factory;

    private $engine;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine)
    {
        $this->factory = $factory;
        $this->engine = $engine;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getHeaderLine('Accept');

        $message = sprintf('Path %s does not exists', $request->getUri()->getPath());

        return strpos($accept, 'application/json') === false
            ? $this->html($message)
            : $this->json($message);
    }

    private function html(string $message): ResponseInterface
    {
        $response = $this->factory
            ->createResponse(404)
            ->withHeader('content-type', 'text/html');

        $body = $this->engine->render('_errors/404', [
            'message' => $message,
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    private function json(string $message): ResponseInterface
    {
        $response = $this->factory
            ->createResponse(404)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write(json_encode([
            'code' => 404,
            'success' => false,
            'reason' => $message,
            'data' => [],
        ]));

        return $response;
    }
}
