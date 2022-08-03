<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\Error;
use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;
use App\Input\Validation\InvalidDataException;

final class Alignment extends ArrayInput implements \JsonSerializable
{
    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('sequence')->string([Mapping::class, 'from']),
            ArrayKey::required('isoforms')->array([IsoformList::class, 'from']),
        );
    }

    public function __construct(
        public readonly Mapping $sequence,
        public readonly IsoformList $isoforms,
    ) {
        $errors = [];

        foreach ($isoforms as $i => $isoform) {
            foreach ($isoform->occurrences as $o => $occurrence) {
                $length = $occurrence->length();

                if ($length < strlen($sequence->value)) {
                    $errors[] = Error::from('%%s must be greater than or equal to sequence length')
                        ->nest('isoforms', (string) $i, 'occurrences', (string) $o);
                }
            }
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            'sequence' => $this->sequence->value,
            'isoforms' => $this->isoforms,
        ];
    }
}
