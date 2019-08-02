<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class Statement
{
    private $generator;

    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
    }

    public function fetch()
    {
        return $this->generator->valid()
            ? $this->generator->current()
            : false;
    }

    public function fetchAll(): array
    {
        return iterator_to_array($this->generator);
    }
}
