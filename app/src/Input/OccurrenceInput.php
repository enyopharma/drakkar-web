<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Field;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Rules\OfType;

final class OccurrenceInput
{
    private int $start;

    private int $stop;

    private float $identity;

    public static function factory(string $subject, string $query): callable
    {
        $is_int = new Guard(new OfType('int'));
        $is_flt = new Guard(new OfType('float'));

        return new Validation(fn (...$xs) => self::from($subject, $query, ...$xs),
            Field::required('start', $is_int)->focus(),
            Field::required('stop', $is_int)->focus(),
            Field::required('identity', $is_flt)->focus(),
        );
    }

    public static function from(string $subject, string $query, int $start, int $stop, float $identity): self
    {
        $input = new self($start, $stop, $identity);

        return validated($input, ...$input->validate($subject, $query));
    }

    private function __construct(int $start, int $stop, float $identity)
    {
        $this->start = $start;
        $this->stop = $stop;
        $this->identity = $identity;
    }

    public function data(): array
    {
        return [
            'start' => $this->start,
            'stop' => $this->stop,
            'identity' => $this->identity,
        ];
    }

    private function validate(string $subject, string $query): array
    {
        $errors = [
            ...nested('start', ...$this->validateStart($subject)),
            ...nested('stop', ...$this->validateStop($subject)),
        ];

        if (count($errors) == 0) {
            $errors = $this->validateCoordinates($query);
        }

        return [...$errors, ...nested('identity', ...$this->validateIdentity())];
    }

    private function validateStart(string $subject): array
    {
        return $this->start < 1
            ? [new Error('must be inside the isoform sequence')]
            : [];
    }

    private function validateStop(string $subject): array
    {
        return $this->stop > strlen($subject)
            ? [new Error('must be inside the isoform sequence')]
            : [];
    }

    private function validateCoordinates(string $query): array
    {
        if ($this->start > $this->stop) {
            return [new Error('stop must be greater than start')];
        }

        if ($this->stop - $this->start + 1 != strlen($query)) {
            return [new Error('occurrence must have the same length as the query sequence')];
        }

        return [];
    }

    private function validateIdentity(): array
    {
        return $this->identity < 96 || $this->identity > 100
            ? [new Error('must be between 96 and 100')]
            : [];
    }
}
