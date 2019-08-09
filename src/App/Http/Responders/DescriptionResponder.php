<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use Domain\Payloads\DomainPayloadInterface as Payload;

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

    public function __invoke(Request $request, Payload $payload): MaybeResponse
    {
        if ($payload instanceof \Domain\Payloads\DescriptionCollection) {
            return $this->descriptionCollectionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\PageOutOfRange) {
            return $this->pageOutOfRange($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\Publication) {
            return $this->publicationData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\Description) {
            return $this->descriptionData($request, $payload);
        }

        return MaybeResponse::none();
    }

    private function descriptionCollectionData($request, $payload): MaybeResponse
    {
        $data = ['descriptions' => $payload->data()] + $payload->meta();

        $body = $this->engine->render('descriptions/index', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return MaybeResponse::just($response);
    }

    private function pageOutOfRange($request, $payload): MaybeResponse
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

        $response = $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);

        return MaybeResponse::just($response);
    }

    private function publicationData($request, $payload): MaybeResponse
    {
        $data = [
            'publication' => $payload->data(),
            'description' => [],
        ] + $payload->meta();

        $body = $this->engine->render('descriptions/form', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return MaybeResponse::just($response);
    }

    private function descriptionData($request, $payload): MaybeResponse
    {
        $data = ['description' => $payload->data()] + $payload->meta();

        $body = $this->engine->render('descriptions/form', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return MaybeResponse::just($response);
    }
}
