<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use App\Domain\SelectRuns;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectRuns $domain, Responder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->domain)()->parsed(function (array $data) {
            return $this->responder->html('runs/index', $data);
        });
    }
}
