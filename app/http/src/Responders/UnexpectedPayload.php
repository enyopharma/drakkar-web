<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Domain\Payloads\DomainPayloadInterface;

final class UnexpectedPayload extends \LogicException
{
    public function __construct(HttpResponderInterface $responder, DomainPayloadInterface $payload)
    {
        parent::__construct(
            vsprintf('Responder %s failed to produce a response for payload %s', [
                get_class($responder),
                get_class($payload),
            ])
        );
    }
}
