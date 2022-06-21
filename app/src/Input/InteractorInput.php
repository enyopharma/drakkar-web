<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class InteractorInput
{
    const NAME_PATTERN = '/^[^\s]+$/';

    public static function fromArray(array $data): self
    {
        $errors = [];

        if (!array_key_exists('protein_id', $data)) $errors[] = Error::nested('protein_id', 'is required');
        if (!array_key_exists('name', $data)) $errors[] = Error::nested('name', 'is required');
        if (!array_key_exists('start', $data)) $errors[] = Error::nested('start', 'is required');
        if (!array_key_exists('stop', $data)) $errors[] = Error::nested('stop', 'is required');
        if (!array_key_exists('mapping', $data)) $errors[] = Error::nested('mapping', 'is required');

        if (!is_int($data['protein_id'] ?? 0)) $errors[] = Error::nested('protein_id', 'must be an int');
        if (!is_string($data['name'] ?? '')) $errors[] = Error::nested('name', 'must be a string');
        if (!is_int($data['start'] ?? 0)) $errors[] = Error::nested('start', 'must be an int');
        if (!is_int($data['stop'] ?? 0)) $errors[] = Error::nested('stop', 'must be an int');
        if (!is_array($data['mapping'] ?? [])) $errors[] = Error::nested('mapping', 'must be an array');

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $protein_id = $data['protein_id'];
        $name = $data['name'];
        $start = $data['start'];
        $stop = $data['stop'];
        $alignments = [];

        foreach ($data['mapping'] as $a => $alignment) {
            try {
                $alignments[] = AlignmentInput::fromArray($alignment);
            } catch (InvalidDataException $e) {
                $es = array_map(fn () => $e->nest('alignments', (string) $a), $e->errors());

                array_push($errors, ...$es);
            }
        }

        return self::from($protein_id, $name, $start, $stop, ...$alignments);
    }

    public static function from(int $protein_id, string $name, int $start, int $stop, AlignmentInput ...$alignments): self
    {
        $input = new self($protein_id, $name, $start, $stop, ...$alignments);

        $errors = [];

        array_push($errors, ...$input->validateProteinId());
        array_push($errors, ...$input->validateName());
        array_push($errors, ...$input->validateCoordinates());
        array_push($errors, ...$input->validateMapping());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    public readonly array $alignments;

    private function __construct(
        public readonly int $protein_id,
        public readonly string $name,
        public readonly int $start,
        public readonly int $stop,
        AlignmentInput ...$alignments,
    ) {
        $this->alignments = $alignments;
    }

    public function data(): array
    {
        return [
            'protein_id' => $this->protein_id,
            'name' => $this->name,
            'start' => $this->start,
            'stop' => $this->stop,
            'mapping' => array_map(fn ($a) => $a->data(), $this->alignments),
        ];
    }

    private function validateProteinId(): array
    {
        return $this->protein_id < 1
            ? [Error::nested('protein_id', 'must be positive')]
            : [];
    }

    private function validateName(): array
    {
        return preg_match(self::NAME_PATTERN, $this->name) === 0
            ? [Error::nested('name', sprintf('must match %s', self::NAME_PATTERN))]
            : [];
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

        return $errors;
    }

    private function validateMapping(): array
    {
        $sequences = array_map(fn ($a) => $a->sequence, $this->alignments);

        if (count($sequences) > count(array_unique($sequences))) {
            return [Error::nested('sequence', 'must be unique')->nest('mapping')];
        }

        return [];
    }
}
