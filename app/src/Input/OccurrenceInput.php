<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class OccurrenceInput
{
    const MIN_LENGTH = 4;

    const MIN_IDENTITY = 96;

    const MAX_IDENTITY = 100;

    public static function fromArray(array $data): self
    {
        $errors = [];

        if (!array_key_exists('start', $data)) $errors[] = Error::nested('start', 'is required');
        if (!array_key_exists('stop', $data)) $errors[] = Error::nested('stop', 'is required');
        if (!array_key_exists('identity', $data)) $errors[] = Error::nested('identity', 'is required');

        if (!is_int($data['start'] ?? 0)) $errors[] = Error::nested('start', 'must be an int');
        if (!is_int($data['stop'] ?? 0)) $errors[] = Error::nested('stop', 'must be an int');
        if (!is_int($data['identity'] ?? 0) && !is_float($data['identity'] ?? 0.0)) {
            $errors[] = Error::nested('start', 'must be an int');
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $start = $data['start'];
        $stop = $data['stop'];
        $identity = $data['identity'];

        return self::from($start, $stop, $identity);
    }

    public static function from(int $start, int $stop, float $identity): self
    {
        $input = new self($start, $stop, $identity);

        $errors = [];

        array_push($errors, ...$input->validateCoordinates());
        array_push($errors, ...$input->validateIdentity());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        public readonly int $start,
        public readonly int $stop,
        public readonly float $identity,
    ) {
    }

    public function length(): int
    {
        return $this->stop - $this->start + 1;
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
}
