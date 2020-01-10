<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use App\Http\UrlGenerator;

final class HtmlResponder
{
    private $factory;

    private $engine;

    private $url;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine, UrlGenerator $url)
    {
        $this->factory = $factory;
        $this->engine = $engine;
        $this->url = $url;
    }

    public function success(string $path, array $data = []): ResponseInterface
    {
        return $this->template(200, $path, $data);
    }

    public function notFound(ServerRequestInterface $request): ResponseInterface
    {
        return $this->template(404, '_errors/404', [
            'method' => $request->getMethod(),
            'url' => $request->getUri(),
        ]);
    }

    public function route(string $name, array $params = [], array $query = [], string $fragment = ''): ResponseInterface
    {
        $url = $this->url->generate($name, $params, $query, $fragment);

        return $this->location($url);
    }

    public function location(string $url): ResponseInterface
    {
        return $this->factory->createResponse(302)->withHeader('location', $url);
    }

    public function template(int $code, string $path, array $data = []): ResponseInterface
    {
        $body = $this->engine->render($path, $data);

        $response = $this->factory->createResponse($code)->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }
}
