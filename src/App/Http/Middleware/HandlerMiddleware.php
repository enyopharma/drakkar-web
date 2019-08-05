<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\DomainActionInterface;

use App\Http\Input\HttpInputInterface;
use App\Http\Responders\MaybeResponse;
use App\Http\Responders\HttpResponderInterface;

final class HandlerMiddleware implements MiddlewareInterface
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $input = ($this->input)($request);

        $payload = ($this->domain)($input);

        $result = ($this->responder)($request, $payload);

        return $result->isNone()
            ? $handler->handle($request)
            : $result->response();
    }
}
