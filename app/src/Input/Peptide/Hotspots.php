<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use App\Input\Validation\Error;
use App\Input\Validation\InvalidDataException;

final class Hotspots
{
    public static function from(array $value): self
    {
        return new self($value);
    }

    public function __construct(public readonly array $value)
    {
        $errors = [];

        $num = false;
        $str = false;

        foreach ($value as $pos => $text) {
            if (!is_int($pos)) $num = true;
            if (!is_string($text)) $str = true;
        }

        if ($num) $errors[] = Error::from('%%s positions must be numeric');
        if ($str) $errors[] = Error::from('%%s descriptions must be string');

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }
    }
}
