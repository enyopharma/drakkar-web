<?php declare(strict_types=1);

namespace App\Repositories;

final class ResultSet implements ResultSetInterface
{
    private $iterator;

    private $constraints;

    public function __construct(iterable $iterable, array $constraints = [])
    {
        $this->iterator = is_array($iterable)
            ? new \ArrayIterator($iterable)
            : new \IteratorIterator($iterable);

        $this->constraints = $constraints;
    }

    public function first(): array
    {
        $this->iterator->rewind();

        return $this->iterator->current();
    }

    public function count(): int
    {
        return iterator_count($this->iterator);
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
        $this->iterator->rewind();

        foreach ($this->iterator as $row) {
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
