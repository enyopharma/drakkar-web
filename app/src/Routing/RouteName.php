<?php

namespace App\Routing;

use Psr\Http\Server\RequestHandlerInterface;

final class RouteName
{
    public function __construct(private string|null $name = null) {}

    public function get(string $pattern): RoutePattern
    {
        return self::many(['GET'], $pattern);
    }

    public function post(string $pattern): RoutePattern
    {
        return self::many(['POST'], $pattern);
    }

    public function put(string $pattern): RoutePattern
    {
        return self::many(['PUT'], $pattern);
    }

    public function delete(string $pattern): RoutePattern
    {
        return self::many(['DELETE'], $pattern);
    }

    public function any(string $pattern): RoutePattern
    {
        return self::many(Methods::ALLOWED, $pattern);
    }

    public function many(array $methods, string $pattern): RoutePattern
    {
        $methods = Methods::from(...array_values($methods));

        return new RoutePattern($this->name, $methods, $pattern);
    }
}
