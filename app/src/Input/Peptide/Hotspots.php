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

        $allpos = array_keys($value);
        $intpos = array_map('is_int', $allpos);

        if (count($intpos) < count($allpos)) {
            $errors[] = Error::from('%%s positions must be numeric');
        }

        foreach ($value as $pos => $text) {
            if (is_int($pos) && !is_string($text)) {
                $errors[] = Error::from('%%s pos %s description must be a string', $pos);
            }
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }
    }
}
