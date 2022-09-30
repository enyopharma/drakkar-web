<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractFloat;

final class Identity extends AbstractFloat
{
    const MIN_IDENTITY = 96;

    const MAX_IDENTITY = 100;

    public function __construct(float $value)
    {
        if ($value < self::MIN_IDENTITY || $value > self::MAX_IDENTITY) {
            throw new InvalidDataException(
                Error::from('{key} must be between %s and %s', ['min' => self::MIN_IDENTITY, 'max' => self::MAX_IDENTITY]),
            );
        }

        parent::__construct($value);
    }
}
