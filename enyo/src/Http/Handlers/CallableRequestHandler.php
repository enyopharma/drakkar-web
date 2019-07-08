<?php declare(strict_types=1);

namespace Enyo\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CallableRequestHandler implements RequestHandlerInterface
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function handle(Request $request): Response
    {
        $response = ($this->callable)($request);

        try {
            return $response;
        }
        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                vsprintf('Return value of callable request handler must implement %s, %s returned', [
                    Response::class,
                    gettype($response),
                ])
            );
        }
    }
}
