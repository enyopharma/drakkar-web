<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\Payloads\DomainPayloadInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

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

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof \Domain\Payloads\PublicationCollection) {
            return $this->publicationCollectionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\PageOutOfRange) {
            return $this->pageOutOfRange($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceUpdated) {
            return $this->resourceUpdated($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function publicationCollectionData($request, $payload): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $data = ['publications' => $payload->data()] + $payload->meta();

        $body = $this->engine->render('publications/index', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }

    private function pageOutOfRange($request, $payload): ResponseInterface
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

    private function resourceUpdated($request, $payload): ResponseInterface
    {
        $body = $request->getParsedBody();

        $url = (string) ($body['_source'] ?? '');

        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);
    }

    private function resourceNotFound($request, $payload): ResponseInterface
    {
        $tpl = <<<EOT
<!doctype html>
<html>
    <head>
        <title>Not found</title>
    </head>
    <body>
        <h1>Not found</h1>
        <p>%s.</p>
    </body>
</html>
EOT;

        ['message' => $message] = $payload->meta();

        $body = sprintf($tpl, $message);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }
}
