<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Enyo\InstanceFactory;

final class ResolvedMiddleware implements MiddlewareInterface
{
    private $container;

    private $value;

    public function __construct(ContainerInterface $container, $value)
    {
        $this->container = $container;
        $this->value = $value;
    }

    public function process(Request $request, Handler $handler): Response
    {
        try {
            $middleware = $this->resolved();
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                vsprintf('Unable to create a middleware from value %s', [
                    preg_replace('/\s+/', ' ', print_r($this->value, true)),
                ])
            );
        }

        return $middleware->process($request, $handler);
    }

    private function resolved(): MiddlewareInterface
    {
        if (is_callable($this->value)) {
            return new CallableMiddleware($this->value);
        }

        if (is_string($this->value)) {
            return $this->container->has($this->value)
                ? new MiddlewareContainerEntry($this->container, $this->value)
                : new AutowiredMiddleware(new InstanceFactory($this->container), $this->value);
        }

        return $this->value;
    }
}
