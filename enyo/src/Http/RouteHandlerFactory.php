<?php declare(strict_types=1);

namespace Enyo\Http;

final class RouteHandlerFactory
{
    private $factory;

    public function __construct(RequestHandlerFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke($value): RouteHandler
    {
        return new RouteHandler(($this->factory)($value));
    }
}
