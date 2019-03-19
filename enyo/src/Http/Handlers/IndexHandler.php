<?php declare(strict_types=1);

namespace Enyo\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;

final class IndexHandler implements RequestHandlerInterface
{
    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->html('index');
    }
}
