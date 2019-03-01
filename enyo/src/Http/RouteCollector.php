<?php declare(strict_types=1);

namespace Enyo\Http;

use Zend\Expressive\Router\RouteCollector as ZendRouteCollector;

final class RouteCollector
{
    private $factory;

    private $collector;

    public function __construct(RouteHandlerFactory $factory, ZendRouteCollector $collector)
    {
        $this->factory = $factory;
        $this->collector = $collector;
    }

    /**
     *
     */
    public function route(string $path, $value, array $methods = null, string $name = null)
    {
        return $this->collector->route($path, ($this->factory)($value), $methods, $name);
    }
    /**
     *
     */
    public function get(string $path, $value, string $name = null)
    {
        return $this->collector->get($path, ($this->factory)($value), $name);
    }
    /**
     *
     */
    public function post(string $path, $value, string $name = null)
    {
        return $this->collector->post($path, ($this->factory)($value), $name);
    }
    /**
     *
     */
    public function put(string $path, $value, string $name = null)
    {
        return $this->collector->put($path, ($this->factory)($value), $name);
    }
    /**
     *
     */
    public function patch(string $path, $value, string $name = null)
    {
        return $this->collector->patch($path, ($this->factory)($value), $name);
    }
    /**
     *
     */
    public function delete(string $path, $value, string $name = null)
    {
        return $this->collector->delete($path, ($this->factory)($value), $name);
    }
    /**
     *
     */
    public function any(string $path, $value, string $name = null)
    {
        return $this->collector->any($path, ($this->factory)($value), $name);
    }

    /**
     *
     */
    public function getRoutes() : array
    {
        return $this->collector->getRoutes();
    }
}
