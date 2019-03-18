<?php declare(strict_types=1);

namespace Enyo\Queue;

final class JobHandlerMap
{
    private $map;

    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function with(string $name, JobHandlerInterface $handler): JobHandlerMap
    {
        return new self(array_merge($this->map, [$name => $handler]));
    }

    public function handler(string $name): JobHandlerInterface
    {
        return $this->map[$name] ?? new DefaultJobHandler($name);
    }
}
