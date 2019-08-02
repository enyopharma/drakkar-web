<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Domain\Payloads\DomainPayloadInterface as Payload;

final class RunResponder implements HttpResponderInterface
{
    private $factory;

    public function __construct(HtmlResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Request $request, Payload $payload): Response
    {
        if ($payload instanceof \Domain\Payloads\RunCollectionData) {
            return $this->factory->template(200, 'runs/index', [
                'user' => (array) $request->getAttribute('user', [
                    'name' => 'Anonymous',
                ]),
                'runs' => $payload->data(),
            ]);
        }

        throw new \LogicException(
            sprintf('Unhandled payload %s', get_class($payload))
        );
    }
}
