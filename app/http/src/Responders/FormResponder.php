<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use Domain\Payloads\DomainData;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class FormResponder implements HttpResponderInterface
{
    private $factory;

    private $engine;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine)
    {
        $this->factory = $factory;
        $this->engine = $engine;
    }

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof DomainData) {
            return $this->domainData($request, $payload);
        }

        if ($payload instanceof ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function domainData(ServerRequestInterface $request, DomainData $payload): ResponseInterface
    {
        $data = $payload->data();
        $meta = $payload->meta();

        $data = key_exists('publication', $meta)
            ? ['description' => $data] + $meta
            : ['publication' => $data, 'description' => []] + $meta;

        $body = $this->engine->render('descriptions/form', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }

    private function resourceNotFound(ServerRequestInterface $request, ResourceNotFound $payload): ResponseInterface
    {
        $response = $this->factory
            ->createResponse(404)
            ->withHeader('content-type', 'text/html');

        $body = $this->engine->render('_errors/404', $payload->meta());

        $response->getBody()->write($body);

        return $response;
    }
}
