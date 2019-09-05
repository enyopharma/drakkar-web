<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class ShutdownMiddleware implements MiddlewareInterface
{
    private $factory;

    private $predicate;

    const BODY = <<<HTML
<!doctype html>
<html>
    <head>
        <title>Site under maintenance</title>
    </head>
    <body>
        <h1>Site under maintenance</h1>
        <p>
            Please come back in a few minutes.
        </p>
    </body>
</html>
HTML;

    public function __construct(ResponseFactoryInterface $factory, callable $predicate)
    {
        $this->factory = $factory;
        $this->predicate = $predicate;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ((bool) ($this->predicate)($request)) {
            $response = $this->factory->createResponse(503);

            $response->getBody()->write(self::BODY);

            return $response;
        }

        return $handler->handle($request);
    }
}
