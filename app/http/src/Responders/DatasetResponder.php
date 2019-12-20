<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\Http\Streams\IteratorStream;

use Domain\Payloads\DomainPayloadInterface;

final class DatasetResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof \Domain\Payloads\DomainData) {
            return $this->domainData($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function domainData($request, $payload)
    {
        ['type' => $type, 'dataset' => $dataset] = $payload->data();

        $filename = sprintf('vinland-%s-%s', $type, date('Y-m-d'));

        return $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/plain')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"')
            ->withBody(IteratorStream::json($dataset));
    }
}
