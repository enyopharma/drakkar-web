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
        if ($payload instanceof \Domain\Payloads\Dataset) {
            return $this->dataset($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function dataset($request, $payload): ResponseInterface
    {
        $filename = sprintf('vinland-%s', date('Y-m-d'));

        ['statement' => $statement] = $payload->data();

        return $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/plain')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"')
            ->withBody(IteratorStream::json($statement));
    }
}
