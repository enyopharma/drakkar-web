<?php

declare(strict_types=1);

namespace App\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use App\Routing\UrlGenerator;

final class HtmlResponder
{
    private ResponseFactoryInterface $factory;

    private Engine $engine;

    private UrlGenerator $url;

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

    public function temporary(string $urlOrName, array $params = [], array $query = [], string $fragment = ''): ResponseInterface
    {
        return $this->redirect(302, $urlOrName, $params, $query, $fragment);
    }

    public function permanent(string $urlOrName, array $params = [], array $query = [], string $fragment = ''): ResponseInterface
    {
        return $this->redirect(301, $urlOrName, $params, $query, $fragment);
    }

    public function notFound(): ResponseInterface
    {
        return $this->response(404);
    }

    public function template(int $code, string $path, array $data): ResponseInterface
    {
        $contents = $this->engine->render($path, $data);

        return $this->response($code, $contents);
    }

    public function redirect(int $code, string $urlOrName, array $params = [], array $query = [], string $fragment = ''): ResponseInterface
    {
        if ($code != 301 && $code != 302) {
            throw new \InvalidArgumentException('code must be 301 or 302');
        }

        $url = $this->url->isDefined($urlOrName)
            ? $this->url->generate($urlOrName, $params, $query, $fragment)
            : $urlOrName;

        return $this->response($code)->withHeader('location', $url);
    }

    public function response(int $code, string $contents = ''): ResponseInterface
    {
        $response = $this->factory
            ->createResponse($code)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($contents);

        return $response;
    }
}
