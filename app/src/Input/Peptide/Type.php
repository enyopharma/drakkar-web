<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractString;

use App\Assertions\ProteinType;

final class Type extends AbstractString
{
    public function __construct(string $value)
    {
        if (!ProteinType::isValid($value)) {
            throw new InvalidDataException(
                Error::from('{key} must be either \'%s\' or \'%s\'', [ProteinType::H, ProteinType::V]),
            );
        }

        parent::__construct($value);
    }
}
