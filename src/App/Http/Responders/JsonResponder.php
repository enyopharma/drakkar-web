<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\Payloads\DomainPayloadInterface as Payload;

final class JsonResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, Payload $payload): MaybeResponse
    {
        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->response(404, $payload);
        }

        if ($payload instanceof \Domain\Payloads\DomainConflict) {
            return $this->response(409, $payload);
        }

        if ($payload instanceof \Domain\Payloads\InputNotValid) {
            return $this->response(422, $payload);
        }

        if ($payload instanceof \Domain\Payloads\RuntimeFailure) {
            return $this->response(500, $payload);
        }

        return $this->response(200, $payload);
    }

    private function response(int $code, Payload $payload): MaybeResponse
    {
        $data = [
            'code' => $code,
            'success' => $code >= 200 && $code < 300,
            'data' => $payload->data(),
        ] + $payload->meta();

        $response = $this->factory
            ->createResponse($code)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write(json_encode($data));

        return MaybeResponse::just($response);
    }
}
