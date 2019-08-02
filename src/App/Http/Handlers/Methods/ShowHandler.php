<?php

declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\SelectMethod;

use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectMethod $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $psimi_id = (string) $request->getAttribute('psimi_id', '');

        $payload = ($this->domain)($psimi_id);

        return ($this->responder)($request, $payload);
    }
}
