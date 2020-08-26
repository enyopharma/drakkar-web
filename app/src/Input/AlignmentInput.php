<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Field;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Rules\OfType;

final class AlignmentInput
{
    const MIN_LENGTH = 4;

    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    private string $sequence;

    private array $isoforms;

    public static function factory(SequenceCache $cache): callable
    {
        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));

        $factory = fn ($sequence, $isoforms) => self::from($cache, $sequence, ...array_values($isoforms));

        return new Validation($factory,
            Field::required('sequence', $is_str)->focus(),
            Field::required('isoforms', $is_arr, Map::merged($is_arr))->focus()
        );
    }

    public static function from(SequenceCache $cache, string $sequence, array ...$isoforms): self
    {
        $input = new self($sequence, ...$isoforms);

        $errors = $input->validate($cache);

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(string $sequence, array ...$isoforms)
    {
        $this->sequence = $sequence;
        $this->isoforms = $isoforms;
    }

    public function data(): array
    {
        return [
            'sequence' => $this->sequence,
            'isoforms' => $this->isoforms,
        ];
    }

    private function validate(SequenceCache $cache): array
    {
        $errors = array_map(fn($e) => $e->nest('sequence'), $this->validateSequence());

        if (count($errors) > 0) return $errors;

        $errors = array_map(fn ($e) => $e->nest('isoforms'), $this->validateIsoformsCount());

        if (count($errors) > 0) return $errors;

        $errors = array_map(fn ($e) => $e->nest('isoforms'), $this->validateIsoforms($cache));

        if (count($errors) > 0) return $errors;

        return array_map(fn ($e) => $e->nest('isoforms'), $this->validateIsoformsUniqueness());
    }

    private function validateSequence(): array
    {
        $errors = [];

        if (strlen($this->sequence) < 4) {
            $errors[] = new Error('must be longer than 3');
        }

        if (preg_match(self::SEQUENCE_PATTERN, $this->sequence) === 0) {
            $errors[] = new Error('must contain only letters');
        }

        return $errors;
    }

    private function validateIsoformsCount(): array
    {
        return count($this->isoforms) == 0
            ? [new Error('must not be empty')]
            : [];
    }

    private function validateIsoforms(SequenceCache $cache): array
    {
        $are_isoforms = Map::merged(IsoformInput::factory($cache, $this->sequence));

        try {
            $are_isoforms($this->isoforms);
        }

        catch (InvalidDataException $e) {
            return $e->errors();
        }

        return [];
    }

    private function validateIsoformsUniqueness(): array
    {
        $errors = [];

        $seen = [];

        foreach ($this->isoforms as ['accession' => $accession]) {
            $nb = $seen[$accession] ?? 0;

            if ($nb == 1) {
                $errors[] = new Error(sprintf('\'%s\' must be present only once', $accession));
            }

            $seen[$accession] = $nb + 1;
        }

        return $errors;
    }
}
