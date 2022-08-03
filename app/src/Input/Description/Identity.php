<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\InvalidDataException;

final class Identity
{
    const MIN_IDENTITY = 96;

    const MAX_IDENTITY = 100;

    public static function from(float $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly float $value)
    {
        if ($value < self::MIN_IDENTITY || $value > self::MAX_IDENTITY) {
            throw InvalidDataException::error('%%s must be between %s and %s', self::MIN_IDENTITY, self::MAX_IDENTITY);
        }
    }
}
