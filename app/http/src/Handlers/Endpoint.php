<?php

declare(strict_types=1);

namespace App\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Http\Input\HttpInputInterface;
use App\Http\Responders\HttpResponderInterface;

use Domain\Actions\DomainActionInterface;

final class Endpoint implements RequestHandlerInterface
{
    private $input;

    private $domain;

    private $responder;

    public function __construct(
        HttpInputInterface $input,
        DomainActionInterface $domain,
        HttpResponderInterface $responder
    ) {
        $this->input = $input;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = ($this->input)($request);

        $payload = ($this->domain)($input);

        return ($this->responder)($request, $payload);
    }
}
