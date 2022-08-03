<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\Error;
use App\Input\Validation\InvalidDataException;

final class Mapping
{
    const MIN_LENGTH = 4;

    const PATTERN = '/^[A-Z]*$/';

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly string $value)
    {
        $errors = [];

        if (strlen($value) < self::MIN_LENGTH) {
            $errors[] = Error::from('%%s length must be at least %s', self::MIN_LENGTH);
        }

        if (preg_match(self::PATTERN, $value) === 0) {
            $errors[] =  Error::from('%%s must match %s', self::PATTERN);
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }
    }
}
