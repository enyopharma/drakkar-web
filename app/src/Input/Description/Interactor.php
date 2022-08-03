<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;
use App\Input\Validation\Common\NonEmptyString;
use App\Input\Validation\Common\PositiveInteger;

final class Interactor extends ArrayInput
{
    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('protein_id')->int([PositiveInteger::class, 'from']),
            ArrayKey::required('name')->string([NonEmptyString::class, 'from']),
            [Coordinates::class, 'from'],
            ArrayKey::required('mapping')->array([AlignmentList::class, 'from']),
        );
    }

    public function __construct(
        public readonly PositiveInteger $protein_id,
        public readonly NonEmptyString $name,
        public readonly Coordinates $coordinates,
        public readonly AlignmentList $mapping,
    ) {
    }
}
