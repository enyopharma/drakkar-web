<?php

declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\UpdatePublicationState;

use App\Http\Responders\PublicationResponder;

final class UpdateHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(UpdatePublicationState $domain, PublicationResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) $request->getParsedBody();

        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');
        $state = $body['state'];
        $annotation = $body['annotation'];

        $payload = ($this->domain)($run_id, $pmid, $state, $annotation);

        return ($this->responder)($request, $payload);
    }
}
