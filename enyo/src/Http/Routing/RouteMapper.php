<?php declare(strict_types=1);

namespace Enyo\Http\Routing;

use Psr\Container\ContainerInterface;

use Zend\Expressive\Router\RouteCollector as ZendRouteCollector;

final class RouteMapper
{
    private $factory;

    private $mapper;

    public function __construct(RouteHandlerFactory $factory, callable $mapper)
    {
        $this->factory = $factory;
        $this->mapper = $mapper;
    }

    public function __invoke(ZendRouteCollector $collector)
    {
        ($this->mapper)(
            new RouteCollector($this->factory, $collector)
        );
    }
}
