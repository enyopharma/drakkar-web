<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $accept = $request->getHeaderLine('accept');

        if (stripos($accept, 'application/json') !== false) {
            return $this->json->notfound();
        }

        return $this->html->notfound();
    }
}
