<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class Statement implements \IteratorAggregate
{
    /**
     * @var int
     */
    private $i;

    /**
     * @var \Iterator<\Domain\ReadModel\EntityInterface>
     */
    private $iterator;

    /**
     * @param iterable<\Domain\ReadModel\EntityInterface> $iterable
     * @return \Domain\ReadModel\Statement
     */
    public static function from(iterable $iterable): self
    {
        if (is_array($iterable)) {
            return new self(new \ArrayIterator($iterable));
        }

        if ($iterable instanceof \IteratorAggregate) {
            return new self(new \IteratorIterator($iterable));
        }

        if ($iterable instanceof \Iterator) {
            return new self($iterable);
        }

        throw new \LogicException;
    }

    /**
     * @param \Iterator<\Domain\ReadModel\EntityInterface> $iterator
     */
    private function __construct(\Iterator $iterator)
    {
        $this->i = 0;
        $this->iterator = $iterator;
    }

    /**
     * @return \Domain\ReadModel\EntityInterface|false
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
     * @return array
     */
    public function fetchAll(): array
    {
        $results = [];

        while ($result = $this->fetch()) {
            $results[] = $result->data();
        };

        return $results;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}
