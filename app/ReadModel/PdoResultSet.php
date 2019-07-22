<?php declare(strict_types=1);

namespace App\ReadModel;

final class PdoResultSet implements ResultSetInterface
{
    private $sth;

    public function __construct(\PDOStatement $sth)
    {
        $this->sth = $sth;
    }

    public function first(): array
    {
        if ($row = $this->sth->fetch()) {
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
        while ($row = $this->sth->fetch()) {
            yield $row;
        }
    }

    public function jsonSerialize()
    {
        return iterator_to_array($this);
    }
}
