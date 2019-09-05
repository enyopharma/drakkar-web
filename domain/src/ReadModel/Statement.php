<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class Statement implements \IteratorAggregate
{
    private $i;

    private $generator;

    public function __construct(\Generator $generator)
    {
        $this->i = 0;
        $this->generator = $generator;
    }

    public function fetch()
    {
        $this->i == 0
            ? $this->generator->rewind()
            : $this->generator->next();

        if ($this->generator->valid()) {
            $this->i++;

            return $this->generator->current();
        }

        return false;
    }

    public function fetchAll(): array
    {
        return iterator_to_array($this->generator);
    }

    public function getIterator()
    {
        return $this->generator;
    }
}
