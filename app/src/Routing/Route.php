<?php

namespace App\Routing;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\RequestHandler;
use Quanta\Http\LazyMiddleware;

final class Route
{
    private string|null $name;

    private Methods $methods;

    private string $pattern;

    private RequestHandlerInterface $handler;

    private array $middleware;


    public static function named(string $name): RouteName
    {
        return new RouteName($name);
    }

    public static function get(string $pattern): RoutePattern
    {
        return (new RouteName)->get($pattern);
    }

    public static function post(string $pattern): RoutePattern
    {
        return (new RouteName)->post($pattern);
    }

    public static function put(string $pattern): RoutePattern
    {
        return (new RouteName)->put($pattern);
    }

    public static function delete(string $pattern): RoutePattern
    {
        return (new RouteName)->delete($pattern);
    }

    public static function any(string $pattern): RoutePattern
    {
        return (new RouteName)->any($pattern);
    }

    public static function many(array $methods, string $pattern): RoutePattern
    {
        return (new RouteName)->many($methods, $pattern);
    }

    public function __construct(
        string|null $name,
        Methods $methods,
        string $pattern,
        RequestHandlerInterface $handler,
        MiddlewareInterface ...$middleware,
    ) {
        $this->name = $name;
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->middleware = $middleware;
    }

    public function isNamed(): bool
    {
        return !is_null($this->name);
    }

    public function name(): string
    {
        if (is_null($this->name)) {
            throw new \LogicException('route has no name');
        }

        return $this->name;
    }

    public function methods(): array
    {
        return $this->methods->values();
    }

    public function pattern(): string
    {
        return $this->pattern;
    }

    public function handler(): RequestHandlerInterface
    {
        return count($this->middleware) > 0
            ? RequestHandler::queue($this->handler, ...$this->middleware)
            : $this->handler;
    }

    public function middleware(MiddlewareInterface|callable $middleware, MiddlewareInterface|callable ...$xs): self
    {
        return new self (
            $this->name,
            $this->methods,
            $this->pattern,
            $this->handler,
            ...$this->middleware,
            ...array_map([$this, 'wrapMiddleware'], [$middleware, ...$xs]),
        );
    }

    private function wrapMiddleware(MiddlewareInterface|callable $middleware): MiddlewareInterface
    {
        return is_callable($middleware)
            ? new LazyMiddleware($middleware)
            : $middleware;
    }
}
