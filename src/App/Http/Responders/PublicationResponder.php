<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use Domain\Payloads\DomainPayloadInterface as Payload;

final class PublicationResponder implements HttpResponderInterface
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
        if ($payload instanceof \Domain\Payloads\PublicationCollectionData) {
            return $this->publicationCollectionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\PageOutOfRange) {
            return $this->pageOutOfRange($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceUpdated) {
            return $this->resourceUpdated($request, $payload);
        }

        return MaybeResponse::none();
    }

    private function publicationCollectionData($request, $payload): MaybeResponse
    {
        $query = (array) $request->getQueryParams();

        $data = ['publications' => $payload->data()] + $payload->meta();

        $body = $this->engine->render('publications/index', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return MaybeResponse::just($response);
    }

    private function pageOutOfRange($request, $payload): MaybeResponse
    {
        $query = (array) $request->getQueryParams();

        $url = $this->url->generate('runs.publications.index', [
            'run_id' => (int) $request->getAttribute('run_id'),
        ], [
            'state' => $query['state'] ?? '',
            'page' => $payload->page(),
            'limit' => $payload->limit(),
        ], 'publications');

        $response = $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);

        return MaybeResponse::just($response);
    }

    private function resourceUpdated($request, $payload): MaybeResponse
    {
        $body = $request->getParsedBody();

        $url = (string) ($body['_source'] ?? '');

        $response = $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);

        return MaybeResponse::just($response);
    }
}
