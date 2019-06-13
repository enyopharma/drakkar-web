<?php declare(strict_types=1);

namespace Enyo\Http\Routing;

use Enyo\Http\Handlers\RequestHandlerFactory;

final class RouteHandlerFactory
{
    private $factory;

    public function __construct(RequestHandlerFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(string $class): RouteHandler
    {
        return new RouteHandler(($this->factory)($class));
    }
}
