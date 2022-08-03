<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use App\Assertions\ProteinType;

use App\Input\Validation\InvalidDataException;

final class Type
{
    public static function from(string $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly string $value)
    {
        if (!ProteinType::isValid($value)) {
            throw InvalidDataException::error(
                '%%s must be either \'%s\' or \'%s\'',
                ProteinType::H,
                ProteinType::V,
            );
        }
    }
}
