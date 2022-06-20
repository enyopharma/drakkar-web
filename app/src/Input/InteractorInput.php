<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\OfType;
use Quanta\Validation\ArrayFactory;
use Quanta\Validation\InvalidDataException;

final class InteractorInput
{
    const NAME_PATTERN = '/^[^\s]+$/';

    private array $alignments;

    public static function factory(): callable
    {
        $factory = function ($protein_id, $name, $start, $stop, array $alignments) {
            return self::from($protein_id, $name, $start, $stop, ...array_values($alignments));
        };

        $is_arr = OfType::guard('array');
        $is_str = OfType::guard('string');
        $is_int = OfType::guard('int');
        $alignment = AlignmentInput::factory();

        return new ArrayFactory(
            $factory,
            Field::required('protein_id', $is_int)->focus(),
            Field::required('name', $is_str)->focus(),
            Field::required('start', $is_int)->focus(),
            Field::required('stop', $is_int)->focus(),
            Field::required('mapping', $is_arr, Map::merged($is_arr, $alignment))->focus(),
        );
    }

    public static function from(int $protein_id, string $name, int $start, int $stop, AlignmentInput ...$alignments): self
    {
        $input = new self($protein_id, $name, $start, $stop, ...$alignments);

        $errors = [
            ...$input->validateProteinId(),
            ...$input->validateName(),
            ...$input->validateCoordinates(),
            ...$input->validateMapping(),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        private int $protein_id,
        private string $name,
        private int $start,
        private int $stop,
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
        $sequences = array_map(fn ($a) => $a->sequence(), $this->alignments);

        if (count($sequences) > count(array_unique($sequences))) {
            return [Error::nested('sequence', 'must be unique')->nest('mapping')];
        }

        return [];
    }
}
