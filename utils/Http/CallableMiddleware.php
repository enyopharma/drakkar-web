<?php declare(strict_types=1);

namespace Utils\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CallableMiddleware implements MiddlewareInterface
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
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
