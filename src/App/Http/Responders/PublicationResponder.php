<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Domain\Payloads\DomainPayloadInterface as Payload;

final class PublicationResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(HtmlResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, Payload $payload): Response
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

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new \LogicException(
            sprintf('Unhandled payload %s', get_class($payload))
        );
    }

    private function publicationCollectionData($request, $payload): Response
    {
        $query = (array) $request->getQueryParams();

        $data = ['publications' => $payload->data()] + $payload->meta();

        return $this->factory->template(200, 'publications/index', $data);
    }

    private function pageOutOfRange($request, $payload): Response
    {
        $query = (array) $request->getQueryParams();

        return $this->factory->route(302, 'runs.publications.index', [
            'run_id' => (int) $request->getAttribute('run_id'),
        ], [
            'state' => $query['state'] ?? '',
            'page' => $payload->page(),
            'limit' => $payload->limit(),
        ], 'publications');
    }

    private function resourceUpdated($request, $payload): Response
    {
        $body = $request->getParsedBody();

        $url = (string) ($body['_source'] ?? '');

        return $this->factory->redirect(302, $url);
    }

    private function resourceNotFound($request, $payload): Response
    {
        return $this->factory->notfound(
            $payload->message()
        );
    }
}
