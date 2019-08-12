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
        if ($this->generator->valid()) {
            $current = $this->generator->current();

            $this->generator->next();

            return $current;
        }

        return false;
    }

    public function fetchAll(): array
    {
        return iterator_to_array($this->generator);
    }
}
