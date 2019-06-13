<?php declare(strict_types=1);

namespace Enyo;

use Psr\Container\ContainerInterface;

final class InstanceFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(string $class)
    {
        try {
            return new $class(
                ...array_map([$this, 'argument'], $this->parameters($class))
            );
        }

        catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to instantiate %s', $class), 0, $e
            );
        }
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
            sprintf('Unable to bind an argument to %s', $parameter)
        );
    }

    private function parameters(string $class): array
    {
        try {
            $reflection = new \ReflectionClass($class);
        }

        catch (\ReflectionException $e) {
            return [];
        }

        $constructor = $reflection->getConstructor();

        return ! is_null($constructor)
            ? $constructor->getParameters()
            : [];
    }
}
