<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LazyMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
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
