<?php declare(strict_types=1);

namespace Enyo\Data;

final class ResultSet implements \IteratorAggregate
{
    private $iterable;

    private $constraints;

    public function __construct(iterable $iterable, array $constraints = [])
    {
        $this->iterable = $iterable;
        $this->constraints = $constraints;
    }

    public function count(): int
    {
        return is_array($this->iterable)
            ? count($this->iterable)
            : iterator_count($this->iterable);
    }

    public function chunks(int $size = 10): \Generator
    {
        $chunk = [];

        foreach ($this as $row) {
            $chunk[] = $row;

            if (count($chunk) == $size) {
                yield $chunk;

                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            yield $chunk;
        }
    }

    public function getIterator()
    {
        foreach ($this->iterable as $row) {
            if ($this->isPassingConstraints($row)) {
                yield $row;
            }
        }
    }

    private function isPassingConstraints(array $row): bool
    {
        foreach ($this->constraints as $field => $value) {
            if ($row[$field] !== $value) {
                return false;
            }
        }

        return true;
    }
}
