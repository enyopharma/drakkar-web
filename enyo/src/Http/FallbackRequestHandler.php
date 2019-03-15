<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class FallbackRequestHandler implements RequestHandlerInterface
{
    private $container;

    private $value;

    public function __construct(ContainerInterface $container, string $value)
    {
        $this->container = $container;
        $this->value = $value;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->container->has($this->value)) {
            $handler = $this->container->get($this->value);

            if ($handler instanceof RequestHandlerInterface) {
                return $handler->handle($request);
            }

            throw new \UnexpectedValueException(
                vsprintf('Container entry \'%s\' must implement %s to be used as a route handler, %s returned', [
                    $this->value,
                    RequestHandlerInterface::class,
                    gettype($handler),
                ])
            );
        }

        if (class_exists($this->value)) {
            $handler = $this->instance($this->value);

            if ($handler instanceof RequestHandlerInterface) {
                return $handler->handle($request);
            }

            throw new \UnexpectedValueException(
                vsprintf('Class %s must implement %s to be used as a route handler', [
                    $this->value,
                    RequestHandlerInterface::class,
                ])
            );
        }

        throw new \UnexpectedValueException(
            vsprintf('Value %s can\'t be resolved as an implementation of %s', [
                $this->value,
                RequestHandlerInterface::class,
            ])
        );
    }

    private function instance(string $class)
    {
        $parameters = $this->parameters($class);

        $arguments = array_map([$this, 'argument'], $parameters);

        return new $class(...$arguments);
    }

    private function parameters(string $class): array
    {
        $reflection = new \ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        return ! is_null($constructor)
            ? $constructor->getParameters()
            : [];
    }

    private function argument(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if (! is_null($type)) {
            if (! $type->isBuiltIn()) {
                return $this->container->get($type->getName());
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new \LogicException(
            vsprintf('No argument for parameter $%s of %s::__construct()', [
                $parameter->getName(),
                $this->value,
            ])
        );
    }
}
