<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractString;

final class Sequence extends AbstractString
{
    const MIN_LENGTH = 5;
    const MAX_LENGTH = 20;
    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    public function __construct(string $value)
    {
        $errors = [];

        if (strlen($value) < self::MIN_LENGTH) {
            $errors[] = Error::from('{key} must be longer than or equal to %s', ['min' => self::MIN_LENGTH]);
        }

        if (strlen($value) > self::MAX_LENGTH) {
            $errors[] = Error::from('{key} must be shorter than or equal to %s', ['max' => self::MAX_LENGTH]);
        }

        if (preg_match(self::SEQUENCE_PATTERN, $value) === 0) {
            $errors[] = Error::from('{key} must match %s', ['pattern' => self::SEQUENCE_PATTERN]);
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        parent::__construct($value);
    }
}
