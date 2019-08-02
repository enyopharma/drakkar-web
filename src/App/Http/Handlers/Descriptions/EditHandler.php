<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\SelectDescription;

use App\Http\Responders\DescriptionResponder;

final class EditHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectDescription $domain, DescriptionResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');
        $id = (int) $request->getAttribute('id');

        $payload = ($this->domain)($run_id, $pmid, $id);

        return ($this->responder)($request, $payload);
    }
}
