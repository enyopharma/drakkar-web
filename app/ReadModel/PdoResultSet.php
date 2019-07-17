<?php declare(strict_types=1);

namespace App\ReadModel;

final class PdoResultSet implements ResultSetInterface
{
    private $select;

    private $fetch_style;

    private $cursor_orientation;

    private $cursor_offset;

    public function __construct(
        \PDOStatement $select,
        int $fetch_style = \PDO::FETCH_BOTH,
        int $cursor_orientation = \PDO::FETCH_ORI_NEXT,
        int $cursor_offset = 0
    ) {
        $this->select = $select;
        $this->fetch_style = $fetch_style;
        $this->cursor_orientation = $cursor_orientation;
        $this->cursor_offset = $cursor_offset;
    }

    public function first(): array
    {
        if ($row = $this->fetch()) {
            return $row;
        }

        throw new NotFoundException;
    }

    public function count(): int
    {
        throw new \LogicException(
            sprintf('%s is not countable', self::class)
        );
    }

    public function getIterator()
    {
        while ($row = $this->fetch()) {
            yield $row;
        }
    }

    public function jsonSerialize()
    {
        return iterator_to_array($this);
    }

    private function fetch()
    {
        return $this->select->fetch(
            $this->fetch_style,
            $this->cursor_orientation,
            $this->cursor_offset
        );
    }
}
