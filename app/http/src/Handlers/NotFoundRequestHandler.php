<?php

declare(strict_types=1);

namespace App\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class NotFoundRequestHandler implements RequestHandlerInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accept = $request->getHeaderLine('Accept');

        $reason = sprintf('Path %s does not exists', $request->getUri()->getPath());

        return strpos($accept, 'application/json') === false
            ? $this->html($reason)
            : $this->json($reason);
    }

    private function html(string $reason): ResponseInterface
    {
        $tpl = <<<EOT
<!doctype html>
<html>
    <head>
        <title>Not found</title>
    </head>
    <body>
        <h1>Not found</h1>
        <p>%s.</p>
    </body>
</html>
EOT;

        $response = $this->factory
            ->createResponse(404)
            ->withHeader('content-type', 'text/html');

        $body = sprintf($tpl, $reason);

        $response->getBody()->write($body);

        return $response;
    }

    private function json(string $reason): ResponseInterface
    {
        $response = $this->factory
            ->createResponse(404)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write(json_encode([
            'code' => 404,
            'success' => false,
            'reason' => $reason,
            'data' => [],
        ]));

        return $response;
    }
}
