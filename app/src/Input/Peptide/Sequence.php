<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use App\Input\Validation\Error;
use App\Input\Validation\InvalidDataException;

final class Sequence
{
    const MIN_LENGTH = 5;
    const MAX_LENGTH = 20;
    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly string $value)
    {
        $errors = [];

        if (strlen($value) < self::MIN_LENGTH) {
            $errors[] = Error::from('%%s must be longer than or equal to %s', self::MIN_LENGTH);
        }

        if (strlen($value) > self::MAX_LENGTH) {
            $errors[] = Error::from('%%s must be shorter than or equal to %s', self::MAX_LENGTH);
        }

        if (preg_match(self::SEQUENCE_PATTERN, $value) === 0) {
            $errors[] = Error::from('%%s must match %s', self::SEQUENCE_PATTERN);
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }
    }
}
