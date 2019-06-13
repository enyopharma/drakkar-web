<?php declare(strict_types=1);

namespace Enyo;

final class InstanceFactoryProxy
{
    private $factory;

    private $class;

    public function __construct(InstanceFactory $factory, string $class)
    {
        $this->factory = $factory;
        $this->class = $class;
    }

    public function __invoke()
    {
        return ($this->factory)($this->class);
    }
}
