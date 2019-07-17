<?php declare(strict_types=1);

namespace App\ReadModel;

final class ArrayResultSet implements ResultSetInterface
{
    private $rows;

    public function __construct(array ...$rows)
    {
        $this->rows = $rows;
    }

    public function first(): array
    {
        if (count($this->rows) > 0) {
            return $this->rows[0];
        }

        throw new NotFoundException;
    }

    public function count(): int
    {
        return count($this->rows);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }

    public function jsonSerialize()
    {
        return $this->rows;
    }
}
