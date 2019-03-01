<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;

use Zend\Expressive\Router\RouteCollector as ZendRouteCollector;

final class RouteMapper
{
    private $mapper;

    public function __construct(callable $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke(ContainerInterface $container, ZendRouteCollector $collector)
    {
        ($this->mapper)(
            new RouteCollector($collector, new RouteHandlerFactory($container))
        );
    }
}
