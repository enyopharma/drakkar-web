<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\VariadicInput;
use App\Input\Validation\VariadicFactory;
use App\Input\Validation\InvalidDataException;

/**
 * @implements \IteratorAggregate<\App\Input\Occurrence>
 */
final class OccurrenceList extends VariadicInput implements \IteratorAggregate, \JsonSerializable
{
    protected static function validation(VariadicFactory $factory): VariadicFactory
    {
        return $factory->array([Occurrence::class, 'from']);
    }

    /**
     * @var \App\Input\Occurrence[]
     */
    public readonly array $occurrences;

    public function __construct(Occurrence ...$occurrences)
    {
        if (count($occurrences) == 0) {
            throw InvalidDataException::error('%%s must not be empty');
        }

        $coordinates = array_map(fn ($o) => implode(':', $o->xy()), $occurrences);

        if (count($coordinates) > count(array_unique($coordinates))) {
            throw InvalidDataException::error('%%s coordinates must be unique');
        }

        $this->occurrences = $occurrences;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->occurrences);
    }

    public function jsonSerialize(): mixed
    {
        return $this->occurrences;
    }
}
