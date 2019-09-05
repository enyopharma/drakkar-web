<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ServerRequestInterface as Request;

use Domain\Payloads\DomainPayloadInterface as Payload;

interface HttpResponderInterface
{
    public function __invoke(Request $request, Payload $payload): MaybeResponse;
}
