<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\Payloads\DomainPayloadInterface;

use League\Plates\Engine;

final class RunResponder implements HttpResponderInterface
{
    private $factory;

    private $engine;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine)
    {
        $this->factory = $factory;
        $this->engine = $engine;
    }

    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface
    {
        if ($payload instanceof \Domain\Payloads\RunCollection) {
            return $this->runCollectionData($request, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($request, $payload);
        }

        throw new UnexpectedPayload($this, $payload);
    }

    private function runCollectionData($request, $payload): ResponseInterface
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
