<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;

final class Interactor extends ArrayInput
{
    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('protein_id')->int([DatabaseId::class, 'from']),
            ArrayKey::required('name')->string([NonEmptyString::class, 'from']),
            [Coordinates::class, 'from'],
            ArrayKey::required('mapping')->array([AlignmentList::class, 'from']),
        );
    }

    public function __construct(
        public readonly DatabaseId $protein_id,
        public readonly NonEmptyString $name,
        public readonly Coordinates $coordinates,
        public readonly AlignmentList $mapping,
    ) {
    }
}
