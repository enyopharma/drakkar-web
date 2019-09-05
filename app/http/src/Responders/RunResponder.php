<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use Domain\Payloads\DomainPayloadInterface as Payload;

final class RunResponder implements HttpResponderInterface
{
    private $factory;

    private $engine;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine)
    {
        $this->factory = $factory;
        $this->engine = $engine;
    }

    public function __invoke(Request $request, Payload $payload): MaybeResponse
    {
        if ($payload instanceof \Domain\Payloads\RunCollection) {
            return $this->runCollectionData($request, $payload);
        }

        return MaybeResponse::none();
    }

    private function runCollectionData($request, $payload): MaybeResponse
    {
        $body = $this->engine->render('runs/index', [
            'user' => (array) $request->getAttribute('user', [
                'name' => 'Anonymous',
            ]),
            'runs' => $payload->data(),
        ]);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return MaybeResponse::just($response);
    }
}
