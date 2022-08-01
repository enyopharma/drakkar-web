<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\InvalidDataException;

final class NonEmptyString
{
    const PATTERN = '/^[^\s]+$/';

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly string $value)
    {
        if (preg_match(self::PATTERN, $value) === 0) {
            throw InvalidDataException::error('%%s must match %s', self::PATTERN);
        }
    }
}
