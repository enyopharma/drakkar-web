<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CallableRequestHandler implements RequestHandlerInterface
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = ($this->callable)($request);

        try {
            return $response;
        }
        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                vsprintf('Return value of callable request handler must implement %s, %s returned', [
                    ResponseInterface::class,
                    gettype($response),
                ])
            );
        }
    }
}
