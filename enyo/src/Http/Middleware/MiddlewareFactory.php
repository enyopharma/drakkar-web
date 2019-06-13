<?php declare(strict_types=1);

namespace Enyo\Http\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

use Enyo\InstanceFactory;
use Enyo\InstanceFactoryProxy;

final class MiddlewareFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(string $class): MiddlewareInterface
    {
        return new LazyMiddleware(
            new InstanceFactoryProxy(
                new InstanceFactory($this->container),
                $class
            )
        );
    }
}
