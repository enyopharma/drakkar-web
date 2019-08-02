<?php

declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\CollectPublications;

use App\Http\Responders\PublicationResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(CollectPublications $domain, PublicationResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $run_id = (int) $request->getAttribute('run_id');
        $state = $query['state'] ?? \Domain\Association::PENDING;
        $page = (int) ($query['page'] ?? 1);
        $limit = (int) ($query['limit'] ?? 20);

        $payload = ($this->domain)($run_id, $state, $page, $limit);

        return ($this->responder)($request, $payload);
    }
}
