<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractString;

final class Mapping extends AbstractString
{
    const MIN_LENGTH = 4;

    const PATTERN = '/^[A-Z]*$/';

    public function __construct(string $value)
    {
        $errors = [];

        if (strlen($value) < self::MIN_LENGTH) {
            $errors[] = Error::from('{key} length must be at least %s', ['min' => self::MIN_LENGTH]);
        }

        if (preg_match(self::PATTERN, $value) === 0) {
            $errors[] =  Error::from('{key} must match %s', ['pattern' => self::PATTERN]);
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        parent::__construct($value);
    }
}
