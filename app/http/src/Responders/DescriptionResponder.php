<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\Payloads\DomainPayloadInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

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

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function descriptionCollectionData($request, $payload): ResponseInterface
    {
        $data = ['descriptions' => $payload->data()] + $payload->meta();

        $body = $this->engine->render('descriptions/index', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }

    private function pageOutOfRange($request, $payload): ResponseInterface
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

    private function publicationData($request, $payload): ResponseInterface
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

        return $response;
    }

    private function descriptionData($request, $payload): ResponseInterface
    {
        $data = ['description' => $payload->data()] + $payload->meta();

        $body = $this->engine->render('descriptions/form', $data);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
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
