<?php declare(strict_types=1);

namespace App\Http\Handlers\Jobs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\StartAlignment;

use Enyo\Http\Responders\JsonResponder;

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

        $query = (string) $body['query'] ?? '';
        $subjects = (array) $body['subjects'] ?? [];

        $payload = ($this->domain)($query, $subjects);

        return $payload->parsed([$this->responder, 'response']);
    }
}
