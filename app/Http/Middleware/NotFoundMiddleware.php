<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Http\Responders\HtmlResponder;
use App\Http\Responders\JsonResponder;

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

        return stripos($accept, 'application/json') === false
            ? $this->html->notfound()
            : $this->json->notfound();
    }
}
