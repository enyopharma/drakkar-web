<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use App\Http\Helpers\UrlHelper;
use Domain\Payloads\DomainPayloadInterface;

final class DescriptionResponder implements HttpResponderInterface
{
    private $factory;

    private $engine;

    private $url;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine, UrlHelper $url)
    {
        $this->factory = $factory;
        $this->engine = $engine;
        $this->url = $url;
    }

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof \Domain\Payloads\DomainDataCollection) {
            return $this->descriptionCollectionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\PageOutOfRange) {
            return $this->pageOutOfRange($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function descriptionCollectionData($request, $payload)
    {
        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $body = $this->engine->render('descriptions/index', [
            'descriptions' => $payload->data()
        ] + $payload->meta());

        $response->getBody()->write($body);

        return $response;
    }

    private function pageOutOfRange($request, $payload)
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $url = $this->url->generate('runs.publications.descriptions.index', [
            'run_id' => $run_id,
            'pmid' => $pmid,
        ], [
            'page' => $payload->page(),
            'limit' => $payload->limit(),
        ], 'descriptions');

        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);
    }

    private function resourceNotFound($request, $payload)
    {
        $response = $this->factory
            ->createResponse(404)
            ->withHeader('content-type', 'text/html');

        $body = $this->engine->render('_errors/404', $payload->meta());

        $response->getBody()->write($body);

        return $response;
    }
}
