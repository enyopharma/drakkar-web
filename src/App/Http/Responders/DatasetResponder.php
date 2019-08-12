<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\Payloads\DomainPayloadInterface as Payload;

use App\Http\Streams\StatementJsonStream;

final class DatasetResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, Payload $payload): MaybeResponse
    {
        if ($payload instanceof \Domain\Payloads\Dataset) {
            return $this->dataset($request, $payload);
        }

        return MaybeResponse::none();
    }

    private function dataset($request, $payload): MaybeResponse
    {
        $filename = sprintf('vinland-%s', date('Y-m-d'));

        ['statement' => $statement] = $payload->data();

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/plain')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"')
            ->withBody(new StatementJsonStream($statement));

        return MaybeResponse::just($response);
    }
}
