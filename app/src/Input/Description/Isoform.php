<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\InvalidDataException;

final class Isoform extends AbstractInput implements \JsonSerializable
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('accession')->string(Accession::class),
            $v->key('occurrences')->variadic(Occurrence::class),
        );
    }

    /**
     * @var \App\Input\Description\Occurrence[]
     */
    public readonly array $occurrences;

    public function __construct(
        public readonly Accession $accession,
        Occurrence ...$occurrences,
    ) {
        $errors = [];

        if (count($occurrences) == 0) {
            $errors[] = Error::from('{key} must not be empty');
        }

        $coordinates = array_map(fn ($o) => implode(':', $o->xy()), $occurrences);

        if (count($coordinates) > count(array_unique($coordinates))) {
            $errors[] = Error::from('{key} coordinates must be unique');
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $this->occurrences = $occurrences;
    }

    public function jsonSerialize(): array
    {
        return [
            'accession' => $this->accession,
            'occurrences' => $this->occurrences,
        ];
    }
}
