<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\VariadicFactory;
use App\Input\Validation\InvalidDataException;

/**
 * @implements \IteratorAggregate<\App\Input\Occurrence>
 */
final class OccurrenceList implements \IteratorAggregate, \JsonSerializable
{
    /**
     * @param mixed[] $data
     */
    public static function from(array $data): self
    {
        $factory = VariadicFactory::class(self::class)->array([Occurrence::class, 'from']);

        return $factory($data);
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
