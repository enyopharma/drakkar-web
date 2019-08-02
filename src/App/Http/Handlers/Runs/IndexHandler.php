<?php

declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\CollectRuns;

use App\Http\Responders\RunResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(CollectRuns $domain, RunResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $payload = ($this->domain)();

        return ($this->responder)($request, $payload);
    }
}
