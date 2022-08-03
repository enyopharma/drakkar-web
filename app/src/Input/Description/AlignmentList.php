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

    public function isSame(array $mapping): bool
    {
        $map1 = [];
        $map2 = [];

        foreach ($mapping as ['sequence' => $sequence, 'isoforms' => $isoforms]) {
            foreach ($isoforms as ['accession' => $accession, 'occurrences' => $occurrences]) {
                foreach ($occurrences as ['start' => $start, 'stop' => $stop, 'identity' => $identity]) {
                    $map1[join(':', [$sequence, $accession, $start, $stop, $identity])] = true;
                }
            }
        }

        foreach ($this->alignments as $alignment) {
            foreach ($alignment->isoforms as $isoform) {
                foreach ($isoform->occurrences as $occurrence) {
                    $map2[join(':', [
                        $alignment->sequence->value,
                        $isoform->accession->value,
                        $occurrence->coordinates->start->value,
                        $occurrence->coordinates->stop->value,
                        $occurrence->identity->value,
                    ])] = true;
                }
            }
        }

        if (count($map1) != count($map2)) return false;

        foreach (array_keys($map1) as $key) {
            if (!array_key_exists($key, $map2)) return false;
        }

        return true;
    }
}
