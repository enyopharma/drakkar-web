<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\InvalidDataException;

final class Alignment extends AbstractInput implements \JsonSerializable
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('sequence')->string(Mapping::class),
            $v->key('isoforms')->variadic(Isoform::class),
        );
    }

    /**
     * @var \App\Input\Description\Isoform[]
     */
    public readonly array $isoforms;

    public function __construct(
        public readonly Mapping $sequence,
        Isoform ...$isoforms,
    ) {
        $errors = [];

        if (count($isoforms) == 0) {
            $errors[] = Error::from('{key} must not be empty');
        }

        $accessions = array_map(fn ($i) => $i->accession->value(), $isoforms);

        if (count($accessions) > count(array_unique($accessions))) {
            $errors[] = Error::from('{key} accessions must be unique');
        }

        foreach ($isoforms as $i => $isoform) {
            foreach ($isoform->occurrences as $o => $occurrence) {
                $length = $occurrence->length();

                if ($length < strlen($sequence->value())) {
                    $errors[] = Error::from('{key} must be greater than or equal to sequence length')
                        ->nested('isoforms', (string) $i, 'occurrences', (string) $o);
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $this->isoforms = $isoforms;
    }

    public function jsonSerialize(): array
    {
        return [
            'sequence' => $this->sequence,
            'isoforms' => $this->isoforms,
        ];
    }
}
