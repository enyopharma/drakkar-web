<?php

namespace App\Routing;

use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\LazyRequestHandler;

final class RoutePattern
{
    public function __construct(
        private string|null $name,
        private Methods $methods,
        private string $pattern,
    ) {}

    public function named(string $name): self
    {
        return new self($name, $this->methods, $this->pattern);
    }

    public function handler(RequestHandlerInterface|callable $handler): Route
    {
        if (is_callable($handler)) {
            $handler = new LazyRequestHandler($handler);
        }

        return new Route(
            $this->name,
            $this->methods,
            $this->pattern,
            $handler,
        );
    }
}
