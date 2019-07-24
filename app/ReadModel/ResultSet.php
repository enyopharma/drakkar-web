<?php

declare(strict_types=1);

namespace App\ReadModel;

final class ResultSet implements ResultSetInterface
{
    private $results;

    public function __construct(array ...$results)
    {
        $this->results = $results;
    }

    public function count(): int
    {
        return count($this->results);
    }

    public function JsonSerialize()
    {
        return iterator_to_array($this);
    }

    public function getIterator()
    {
        yield from $this->results;
    }
}
