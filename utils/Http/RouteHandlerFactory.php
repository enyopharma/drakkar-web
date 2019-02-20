<?php declare(strict_types=1);

namespace Utils\Http;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\MiddlewareInterface;

final class RouteHandlerFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($value): MiddlewareInterface
    {
        $handler = is_callable($value)
            ? new CallableRequestHandler($value)
            : new FallbackRequestHandler($this->container, $value);

        return new RouteHandler($handler);
    }
}
