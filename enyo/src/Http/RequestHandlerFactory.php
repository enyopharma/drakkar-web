<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestHandlerFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($value): RequestHandlerInterface
    {
        return new ResolvedRequestHandler($this->container, $value);
    }
}
