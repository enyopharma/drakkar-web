<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractString;

final class StableId extends AbstractString
{
    const PATTERN = '/^EY[A-Z0-9]{8}$/';

    public function __construct(string $value)
    {
        if (strlen($value) > 0 && preg_match(self::PATTERN, $value) === 0) {
            throw new InvalidDataException(
                Error::from('{key} must match %s', ['pattern' => self::PATTERN])
            );
        }

        parent::__construct($value);
    }
}
