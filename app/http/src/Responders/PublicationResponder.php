<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use App\Http\UrlGenerator;
use Domain\Payloads\PageOutOfRange;
use Domain\Payloads\ResourceUpdated;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainDataCollection;
use Domain\Payloads\DomainPayloadInterface;

final class PublicationResponder implements HttpResponderInterface
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

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof DomainDataCollection) {
            return $this->domainDataCollection($request, $payload);
        }

        if ($payload instanceof PageOutOfRange) {
            return $this->pageOutOfRange($request, $payload);
        }

        if ($payload instanceof ResourceUpdated) {
            return $this->resourceUpdated($request, $payload);
        }

        if ($payload instanceof ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function domainDataCollection(ServerRequestInterface $request, DomainDataCollection $payload): ResponseInterface
    {
        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $body = $this->engine->render('publications/index', [
            'publications' => $payload->data(),
        ] + $payload->meta());

        $response->getBody()->write($body);

        return $response;
    }

    private function pageOutOfRange(ServerRequestInterface $request, PageOutOfRange $payload): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $url = $this->url->generate('runs.publications.index', [
            'run_id' => (int) $request->getAttribute('run_id'),
        ], [
            'state' => $query['state'] ?? '',
            'page' => $payload->page(),
            'limit' => $payload->limit(),
        ], 'publications');

        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);
    }

    private function resourceUpdated(ServerRequestInterface $request, ResourceUpdated $payload): ResponseInterface
    {
        $body = $request->getParsedBody();

        $url = (string) ($body['_source'] ?? '');

        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);
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
