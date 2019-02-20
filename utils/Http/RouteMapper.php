<?php declare(strict_types=1);

namespace Utils\Http;

use Psr\Container\ContainerInterface;

use Zend\Expressive\Router\RouteCollector as ZendRouteCollector;

final class RouteMapper
{
    private $container;

    private $mapper;

    public function __construct(ContainerInterface $container, callable $mapper)
    {
        $this->container = $container;
        $this->mapper = $mapper;
    }

    public function __invoke(ZendRouteCollector $collector)
    {
        ($this->mapper)(
            new RouteCollector(
                $collector,
                new RouteHandlerFactory($this->container)
            )
        );
    }
}
