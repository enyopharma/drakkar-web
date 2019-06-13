<?php declare(strict_types=1);

namespace Enyo\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LazyMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function process(Request $request, Handler $handler): Response
    {
        $middleware = ($this->factory)();

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $handler);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s expects an instance of %s to be returned by the factory, %s returned', [
                LazyMiddleware::class,
                MiddlewareInterface::class,
                gettype($middleware),
            ])
        );
    }
}
