<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\VariadicInput;
use App\Input\Validation\VariadicFactory;
use App\Input\Validation\InvalidDataException;

/**
 * @implements \IteratorAggregate<\App\Input\Description\Alignment>
 */
final class AlignmentList extends VariadicInput implements \IteratorAggregate, \JsonSerializable
{
    protected static function validation(VariadicFactory $factory): VariadicFactory
    {
        return $factory->array([Alignment::class, 'from']);
    }

    /**
     * @var \App\Input\Description\Alignment[]
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
