<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewareFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($value): MiddlewareInterface
    {
        return new ResolvedMiddleware($this->container, $value);
    }
}
