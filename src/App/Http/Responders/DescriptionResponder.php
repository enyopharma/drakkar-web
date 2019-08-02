<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Domain\Payloads\DomainPayloadInterface as Payload;

final class DescriptionResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(HtmlResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, Payload $payload): Response
    {
        if ($payload instanceof \Domain\Payloads\DescriptionCollectionData) {
            return $this->descriptionCollectionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\PageOutOfRange) {
            return $this->pageOutOfRange($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\PublicationData) {
            return $this->publicationData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\DescriptionData) {
            return $this->descriptionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new \LogicException(
            sprintf('Unhandled payload %s', get_class($payload))
        );
    }

    private function descriptionCollectionData($request, $payload): Response
    {
        $data = ['descriptions' => $payload->data()] + $payload->meta();

        return $this->factory->template(200, 'descriptions/index', $data);
    }

    private function pageOutOfRange($request, $payload): Response
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        return $this->factory->route(302, 'runs.publications.descriptions.index', [
            'run_id' => $run_id,
            'pmid' => $pmid,
        ], [
            'page' => $payload->page(),
            'limit' => $payload->limit(),
        ], 'descriptions');
    }

    private function publicationData($request, $payload): Response
    {
        $data = [
            'publication' => $payload->data(),
            'description' => [],
        ] + $payload->meta();

        return $this->factory->template(200, 'descriptions/form', $data);
    }

    private function descriptionData($request, $payload): Response
    {
        $data = ['description' => $payload->data()] + $payload->meta();

        return $this->factory->template(200, 'descriptions/form', $data);
    }

    private function resourceNotFound($request, $payload): Response
    {
        return $this->factory->notfound(
            $payload->message()
        );
    }
}
