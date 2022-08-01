<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;

final class Isoform extends ArrayInput implements \JsonSerializable
{
    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('accession')->string([Accession::class, 'from']),
            ArrayKey::required('occurrences')->array([OccurrenceList::class, 'from']),
        );
    }

    public function __construct(
        public readonly Accession $accession,
        public readonly OccurrenceList $occurrences,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'accession' => $this->accession->value,
            'occurrences' => $this->occurrences,
        ];
    }
}
