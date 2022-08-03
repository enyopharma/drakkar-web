<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\InvalidDataException;

final class Accession
{
    const PATTERN = '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly string $value)
    {
        if (strlen($value) > 0 && preg_match(self::PATTERN, $value) === 0) {
            throw InvalidDataException::error('%%s must match %s', self::PATTERN);
        }
    }
}
