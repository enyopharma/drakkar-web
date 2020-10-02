<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\OfType;
use Quanta\Validation\ErrorList;
use Quanta\Validation\ArrayFactory;
use Quanta\Validation\InvalidDataException;

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
        $is_int = OfType::guard('int');
        $is_flt = OfType::guard('float');

        return new ArrayFactory([self::class, 'from'],
            Field::required('start', $is_int)->focus(),
            Field::required('stop', $is_int)->focus(),
            Field::required('identity', $is_flt)->focus(),
        );
    }

    public static function from(int $start, int $stop, float $identity): self
    {
        $input = new self($start, $stop, $identity);

        $errors = [
            ...$input->validateCoordinates(),
            ...$input->validateIdentity(),
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
            $errors[] = Error::nested('start', 'must be positive');
        }

        if ($this->stop < 1) {
            $errors[] = Error::nested('stop', 'must be positive');
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
            ? [Error::nested('identity', sprintf('must be between %s and %s', self::MIN_IDENTITY, self::MAX_IDENTITY))]
            : [];
    }

    public function validateForSequence(string $sequence): ErrorList
    {
        $errors = $this->stop - $this->start + 1 != strlen($sequence)
            ? [new Error('must have the same length as sequence')]
            : [];

        return new ErrorList(...$errors);
    }

    public function validateForSubject(string $subject): ErrorList
    {
        $errors = $this->stop > strlen($subject)
            ? [new Error('must be smaller than subject')]
            : [];
        
        return new ErrorList(...$errors);
    }
}
