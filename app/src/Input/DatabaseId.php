<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\InvalidDataException;

final class DatabaseId
{
    public static function from(int $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly int $value)
    {
        if ($value < 1) {
            throw InvalidDataException::error('%%s must be positive');
        }
    }
}
