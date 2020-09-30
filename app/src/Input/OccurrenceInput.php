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
    const MIN_LENGTH = 4;

    const MIN_IDENTITY = 96;

    const MAX_IDENTITY = 100;

    private int $start;

    private int $stop;

    private float $identity;

    public static function factory(): callable
    {
        return new Validation([self::class, 'from'],
            Field::required('start', new Guard(new OfType('int')))->focus(),
            Field::required('stop', new Guard(new OfType('int')))->focus(),
            Field::required('identity', new Guard(new OfType('float')))->focus(),
        );
    }

    public static function from(int $start, int $stop, float $identity): self
    {
        $input = new self($start, $stop, $identity);

        $errors = [
            ...$input->validateCoordinates(),
            ...array_map(fn ($e) => $e->nest('identity'), $input->validateIdentity())
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(int $start, int $stop, float $identity)
    {
        $this->start = $start;
        $this->stop = $stop;
        $this->identity = $identity;
    }

    public function coordinates(): array
    {
        return [$this->start, $this->stop];
    }

    public function data(): array
    {
        return [
            'start' => $this->start,
            'stop' => $this->stop,
            'identity' => $this->identity,
        ];
    }

    private function validateCoordinates(): array
    {
        $errors = [];

        if ($this->start < 1) {
            $errors[] = (new Error('must be positive'))->nest('start');
        }

        if ($this->stop < 1) {
            $errors[] = (new Error('must be positive'))->nest('stop');
        }

        if (count($errors) == 0 && $this->start > $this->stop) {
            $errors[] = new Error('start must be smaller than stop');
        }

        if (count($errors) == 0 && $this->stop - $this->start + 1 < self::MIN_LENGTH) {
            $errors[] = new Error(sprintf('length must be at least %s', self::MIN_LENGTH));
        }

        return $errors;
    }

    private function validateIdentity(): array
    {
        return $this->identity < self::MIN_IDENTITY || $this->identity > self::MAX_IDENTITY
            ? [new Error(sprintf('must be between %s and %s', self::MIN_IDENTITY, self::MAX_IDENTITY))]
            : [];
    }

    public function validateForSequence(string $sequence): array
    {
        return $this->stop - $this->start + 1 != strlen($sequence)
            ? [new Error('must have the same length as sequence')]
            : [];
    }

    public function validateForSubject(string $subject): array
    {
        return $this->stop > strlen($subject)
            ? [new Error('must be smaller than subject')]
            : [];
    }
}
