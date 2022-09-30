<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class Hotspots
{
    public function __construct(private array $data)
    {
        $errors = [];

        $allpos = array_keys($data);
        $intpos = array_map('is_int', $allpos);

        if (count($intpos) < count($allpos)) {
            $errors[] = Error::from('{key} positions must be numeric');
        }

        foreach ($data as $pos => $text) {
            if (is_int($pos) && !is_string($text)) {
                $errors[] = Error::from('{key} pos %s description must be a string', ['pos' => $pos]);
            }
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }
    }

    public function data(): array
    {
        return $this->data;
    }
}
