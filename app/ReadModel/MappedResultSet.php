<?php declare(strict_types=1);

namespace App\ReadModel;

final class MappedResultSet implements ResultSetInterface
{
    private $rset;

    private $mapper;

    private $xs;

    public function __construct(ResultSetInterface $rset, callable $mapper, ...$xs)
    {
        $this->rset = $rset;
        $this->mapper = $mapper;
        $this->xs = $xs;
    }

    public function first(): array
    {
        return ($this->mapper)($this->rset->first(), ...$this->xs);
    }

    public function count(): int
    {
        return $this->rset->count();
    }

    public function getIterator()
    {
        foreach ($this->rset as $row) {
            yield ($this->mapper)($row, ...$this->xs);
        }
    }

    public function jsonSerialize()
    {
        return iterator_to_array($this);
    }
}
