<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Payloads\DomainPayloadInterface;

interface HttpResponderInterface
{
    public function __invoke(ServerRequestInterface $request, DomainPayloadInterface $payload): ResponseInterface;
}
