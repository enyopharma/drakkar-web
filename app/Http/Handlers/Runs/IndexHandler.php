<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\SelectRuns;

use Enyo\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectRuns $domain, HtmlResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->domain)()->parsed(function (array $data) {
            return $this->responder->template('runs/index', $data);
        });
    }
}
