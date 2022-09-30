<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractString;

final class Accession extends AbstractString
{
    const PATTERN = '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';

    public function __construct(string $value)
    {
        if (preg_match(self::PATTERN, $value) === 0) {
            throw new InvalidDataException(
                Error::from('{key} must match %s', ['pattern' => self::PATTERN]),
            );
        }

        parent::__construct($value);
    }
}
