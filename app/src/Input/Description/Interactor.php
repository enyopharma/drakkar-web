<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\InvalidDataException;

final class Interactor extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('protein_id')->int(DatabaseId::class),
            $v->key('name')->string()->trimmed()->rule(ProteinName::class),
            $v->rule(Coordinates::class),
            $v->key('mapping')->variadic(Alignment::class),
        );
    }

    /**
     * @var \App\Input\Description\Alignment[]
     */
    public readonly array $alignments;

    public function __construct(
        public readonly DatabaseId $protein_id,
        public readonly ProteinName $name,
        public readonly Coordinates $coordinates,
        Alignment ...$alignments,
    ) {
        $sequences = array_map(fn ($a) => $a->sequence->value(), $alignments);

        if (count($sequences) > count(array_unique($sequences))) {
            throw new InvalidDataException(Error::from('{key} sequences must be unique'));
        }

        $this->alignments = $alignments;
    }

    public function hasSameMapping(array $mapping): bool
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
                        $alignment->sequence->value(),
                        $isoform->accession->value(),
                        $occurrence->coordinates->start->value(),
                        $occurrence->coordinates->stop->value(),
                        $occurrence->identity->value(),
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
