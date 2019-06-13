<?php declare(strict_types=1);

namespace Enyo\Http\Handlers;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Enyo\InstanceFactory;
use Enyo\InstanceFactoryProxy;

final class RequestHandlerFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(string $class): RequestHandlerInterface
    {
        return new LazyRequestHandler(
            new InstanceFactoryProxy(
                new InstanceFactory($this->container),
                $class
            )
        );
    }
}
