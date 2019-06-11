<?php declare(strict_types=1);

namespace Enyo\ReadModel;

final class ResultSet implements ResultSetInterface
{
    private $rset;

    public function __construct(array $rset)
    {
        $this->rset = $rset;
    }

    public function count(): int
    {
        return count($this->rset);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->rset);
    }

    public function jsonSerialize()
    {
        return $this->rset;
    }

    public function map(callable $cb): ResultSetInterface
    {
        return new ResultSet(array_map($cb, $this->rset));
    }
}
