<?php

declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\SearchProteins;

use App\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SearchProteins $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $type = $query['type'] ?? '';
        $q = $query['q'] ?? '';
        $limit = (int) ($query['limit'] ?? 5);

        $payload = ($this->domain)($type, $q, $limit);

        return ($this->responder)($request, $payload);
    }
}
