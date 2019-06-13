<?php declare(strict_types=1);

namespace Enyo\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Enyo\Http\Responders\HtmlResponder;
use Enyo\Http\Responders\JsonResponder;

final class NotFoundMiddleware implements MiddlewareInterface
{
    private $html;

    private $json;

    public function __construct(HtmlResponder $html, JsonResponder $json)
    {
        $this->html = $html;
        $this->json = $json;
    }

    public function process(Request $request, Handler $handler): Response
    {
        $accept = $request->getHeaderLine('accept');

        return stripos($accept, 'application/json') === false
            ? $this->html->notfound()
            : $this->json->notfound();
    }
}
