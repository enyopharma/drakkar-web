<?php declare(strict_types=1);

namespace Enyo\Cli;

use Psr\Container\ContainerInterface;

use Symfony\Component\Console\Command\Command;

use Enyo\InstanceFactory;

final class CommandFactory
{
    private $container;

    private $factory;

    public function __construct(ContainerInterface $container, InstanceFactory $factory)
    {
        $this->container = $container;
        $this->factory = $factory;
    }

    public function __invoke($value): Command
    {
        if (is_string($value) && $this->container->has($value)) {
            try {
                $command = $this->container->get($value);
            }

            catch (\Throwable $e) {
                throw new \LogicException(
                    sprintf('Unable to use container entry \'%s\' as a command', $value), 0, $e
                );
            }

            try {
                return $command;
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    vsprintf('Container entry \'%s\' must implement %s to be used as a command, %s returned', [
                        $value,
                        Command::class,
                        is_object($command)
                            ? sprintf('instance of %s', get_class($command))
                            : gettype($command),
                    ])
                );
            }
        }

        if (is_string($value)) {
            try {
                $command = ($this->factory)($value);
            }

            catch (\Throwable $e) {
                throw new \RuntimeException(
                    sprintf('Unable to instantiate a command from class %s', $value), 0, $e
                );
            }

            try {
                return $command;
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    vsprintf('Class %s must implement %s to be used as a command', [
                        $value,
                        Command::class,
                    ])
                );
            }
        }

        try {
            return $value;
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                vsprintf('Unable to create a command from value %s', [
                    preg_replace('/\s+/', ' ', print_r($value, true)),
                ])
            );
        }
    }
}
