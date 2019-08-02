<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\SelectPublication;

use App\Http\Responders\DescriptionResponder;

final class CreateHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectPublication $domain, DescriptionResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $payload = ($this->domain)($run_id, $pmid);

        return ($this->responder)($request, $payload);
    }
}
