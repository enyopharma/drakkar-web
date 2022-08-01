<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\Collection;
use App\Input\Validation\InvalidDataException;
use App\Input\Validation\VariadicFactory;

/**
 * @implements \IteratorAggregate<\App\Input\Alignment>
 */
final class AlignmentList implements \IteratorAggregate, \JsonSerializable
{
    /**
     * @param mixed[] $data
     */
    public static function from(array $data): self
    {
        $factory = VariadicFactory::class(self::class)->array([Alignment::class, 'from']);

        return $factory($data);
    }

    /**
     * @var \App\Input\Alignment[]
     */
    public readonly array $alignments;

    public function __construct(Alignment ...$alignments)
    {
        $sequences = array_map(fn ($a) => $a->sequence->value, $alignments);

        if (count($sequences) > count(array_unique($sequences))) {
            throw InvalidDataException::error('%%s sequences must be unique');
        }

        $this->alignments = $alignments;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->alignments);
    }

    public function jsonSerialize(): mixed
    {
        return $this->alignments;
    }
}
