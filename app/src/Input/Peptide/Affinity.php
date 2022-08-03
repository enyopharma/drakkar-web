<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;

final class Affinity extends ArrayInput
{
    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('type')->string(),
            ArrayKey::optional('value')->float(),
            ArrayKey::required('unit')->string(),
        );
    }

    public function __construct(
        public readonly string $type,
        public readonly ?float $value,
        public readonly string $unit,
    ) {
    }
}
