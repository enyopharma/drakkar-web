<?php declare(strict_types=1);

namespace Enyo\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CallableMiddleware implements MiddlewareInterface
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function process(Request $request, Handler $handler): Response
    {
        $response = ($this->callable)($request, $handler);

        try {
            return $response;
        }
        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                vsprintf('Return value of callable middleware must implement %s, %s returned', [
                    ResponseInterface::class,
                    gettype($response),
                ])
            );
        }
    }
}
