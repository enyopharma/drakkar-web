<?php

declare(strict_types=1);

namespace App\Http\Handlers\Jobs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\StartAlignment;

use App\Http\Responders\JsonResponder;

final class AlignmentHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(StartAlignment $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) $request->getParsedBody();

        $id = (string) ($body['id'] ?? '');
        $query = (string) ($body['query'] ?? '');
        $sequences = (array) ($body['sequences'] ?? []);

        $payload = ($this->domain)($id, $query, $sequences);

        return ($this->responder)($request, $payload);
    }
}
