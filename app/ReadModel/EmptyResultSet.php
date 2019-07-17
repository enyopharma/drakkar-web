<?php declare(strict_types=1);

namespace App\ReadModel;

final class EmptyResultSet implements ResultSetInterface
{
    private $name;

    private $criteria;

    public function __construct(string $name, array $criteria = [])
    {
        $this->name = $name;
        $this->criteria = $criteria;
    }

    public function first(): array
    {
        throw new NotFoundException(
            count($this->criteria) > 0
                ? sprintf('%s - no entry matching [%s]', $this->name, $this->criteriaStr())
                : sprintf('%s - no entry found', $this->name)
        );
    }

    public function count(): int
    {
        return 0;
    }

    public function getIterator()
    {
        return new \ArrayIterator([]);
    }

    public function jsonSerialize()
    {
        return [];
    }

    private function criteriaStr(): string
    {
        $keys = array_keys($this->criteria);
        $values = array_values($this->criteria);

        return implode(', ', array_map(function ($k, $v) {
            return sprintf('\'%s\' => %s', $k, $v);
        }, $keys, $values));
    }
}
