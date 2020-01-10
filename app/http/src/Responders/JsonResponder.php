<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class JsonResponder
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function success(array $data = []): ResponseInterface
    {
        return $this->response(200, $data);
    }

    public function errors(string ...$errors): ResponseInterface
    {
        return $this->response(422, [], ['errors' => $errors]);
    }

    public function conflict(string $reason): ResponseInterface
    {
        return $this->response(409, [], ['reason' => $reason]);
    }

    public function notFound(ServerRequestInterface $request): ResponseInterface
    {
        return $this->response(404);
    }

    public function response(int $code, array $data = [], array $meta = []): ResponseInterface
    {
        $body = json_encode([
            'code' => $code,
            'success' => $code >= 200 && $code < 300,
            'data' => $data,
        ] + $meta);

        if ($body === false) {
            throw new \Exception(json_last_error_msg());
        }

        $response = $this->factory
            ->createResponse($code)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write($body);

        return $response;
    }
}
