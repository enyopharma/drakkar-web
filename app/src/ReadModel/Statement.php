<?php

declare(strict_types=1);

namespace App\ReadModel;

/**
 * @implements \IteratorAggregate<array>
 */
final class Statement implements \IteratorAggregate
{
    /**
     * @var int
     */
    private int $i;

    /**
     * @var \Iterator<array>
     */
    private \Iterator $iterator;

    /**
     * @param iterable<array> $iterable
     * @return \App\ReadModel\Statement
     */
    public static function from(iterable $iterable): self
    {
        if (is_array($iterable)) {
            return new self(new \ArrayIterator($iterable));
        }

        if ($iterable instanceof \Iterator) {
            return new self($iterable);
        }

        return new self(new \IteratorIterator($iterable));
    }

    /**
     * @param \Iterator<array> $iterator
     */
    private function __construct(\Iterator $iterator)
    {
        $this->i = 0;
        $this->iterator = $iterator;
    }

    /**
     * @return array|false
     */
    public function fetch()
    {
        $this->i == 0
            ? $this->iterator->rewind()
            : $this->iterator->next();

        if ($this->iterator->valid()) {
            $this->i++;

            return $this->iterator->current();
        }

        return false;
    }

    /**
     * @return array[]
     */
    public function fetchAll(): array
    {
        return iterator_to_array($this->iterator);
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \Traversable
    {
        return $this->iterator;
    }
}
