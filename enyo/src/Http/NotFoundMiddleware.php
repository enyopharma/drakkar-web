<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class NotFoundMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $accept = $request->getHeaderLine('accept');

        $response = $this->factory->createResponse(404, 'Not found');

        if (stripos($accept, 'text/html') !== false) {
            return $this->html($response);
        }

        if (stripos($accept, 'application/json') !== false) {
            return $this->json($response);
        }

        return $this->plain($response);
    }

    private function html(ResponseInterface $response): ResponseInterface
    {
        $body = $response->getBody();

        $body->write(<<<EOT
<!DOCTYPE html>
<html>
    <head>
        <title>Not found</title>
    </head>
    <body>
        <h1>Not found</h1>
    </body>
</html>
EOT
        );

        return $response
            ->withHeader('content-type', 'text/html')
            ->withBody($body);
    }

    private function json(ResponseInterface $response): ResponseInterface
    {
        $body = $response->getBody();

        $body->write(json_encode(new Contents\Json([], 404, 'Not found')));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withBody($body);
    }

    private function plain(ResponseInterface $response): ResponseInterface
    {
        $body = $response->getBody();

        $body->write('404 - not found');

        return $response
            ->withHeader('content-type', 'text/plain')
            ->withBody($body);
    }
}
