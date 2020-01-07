<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\Payloads\InputNotValid;
use Domain\Payloads\DomainConflict;
use Domain\Payloads\RuntimeFailure;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class JsonResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof ResourceNotFound) {
            return $this->response(404, $payload);
        }

        if ($payload instanceof DomainConflict) {
            return $this->response(409, $payload);
        }

        if ($payload instanceof InputNotValid) {
            return $this->response(422, $payload);
        }

        if ($payload instanceof RuntimeFailure) {
            return $this->response(500, $payload);
        }

        return $this->response(200, $payload);
    }

    private function response(int $code, DomainPayloadInterface $payload): ResponseInterface
    {
        $data = [
            'code' => $code,
            'success' => $code >= 200 && $code < 300,
            'data' => $payload->data(),
        ] + $payload->meta();

        $response = $this->factory
            ->createResponse($code)
            ->withHeader('content-type', 'application/json');

        $body = ($tmp = json_encode($data)) === false ? '' : $tmp;

        $response->getBody()->write($body);

        return $response;
    }
}
