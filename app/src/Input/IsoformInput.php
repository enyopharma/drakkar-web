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

final class IsoformInput
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';

    private string $accession;

    private array $occurrences;

    public static function factory(): callable
    {
        $factory = fn ($accession, $occurrences) => self::from($accession, ...array_values($occurrences));

        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));
        $occurrence = OccurrenceInput::factory();

        return new Validation($factory,
            Field::required('accession', $is_str)->focus(),
            Field::required('occurrences', $is_arr, Map::merged($is_arr, $occurrence))->focus(),
        );
    }

    public static function from(string $accession, OccurrenceInput ...$occurrences): self
    {
        $input = new self($accession, ...$occurrences);

        $errors = [
            ...array_map(fn ($e) => $e->nest('accession'), $input->validateAccession()),
            ...array_map(fn ($e) => $e->nest('occurrences'), $input->validateOccurrences()),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(string $accession, OccurrenceInput ...$occurrences)
    {
        $this->accession = $accession;
        $this->occurrences = $occurrences;
    }

    public function accession(): string
    {
        return $this->accession;
    }

    public function data(): array
    {
        return [
            'accession' => $this->accession,
            'occurrences' => array_map(fn ($o) => $o->data(), $this->occurrences),
        ];
    }

    private function validateAccession(): array
    {
        return preg_match(self::ACCESSION_PATTERN, $this->accession) === 0
            ? [new Error(sprintf('must match %s', self::ACCESSION_PATTERN))]
            : [];
    }

    private function validateOccurrences(): array
    {
        $errors = [];

        if (count($this->occurrences) == 0) {
            $errors[] = (new Error('must not be empty'))->nest('occurrences');
        }

        $seen = [];

        foreach ($this->occurrences as $occurrence) {
            [$start, $stop] = $occurrence->coordinates();

            $nb = $seen[$start][$stop] ?? 0;

            if ($nb == 1) {
                $errors[] = new Error(sprintf('[%s, %s] must be present only once', $start, $stop));
            }

            $seen[$start][$stop] = $nb + 1;
        }

        return $errors;
    }

    public function validateForSequence(string $sequence): array
    {
        $errors = [];

        foreach ($this->occurrences as $i => $occurrence) {
            $errors = [...$errors, ...array_map(fn ($e) => $e->nest((string) $i), $occurrence->validateForSequence($sequence))];
        }

        return array_map(fn ($e) => $e->nest('occurrences'), $errors);
    }

    public function validateForSubjects(array $subjects): array
    {
        if (!array_key_exists($this->accession, $subjects)) {
            return [(new Error('must be associated with the interactor'))->nest('accession')];
        }

        $errors = [];

        foreach ($this->occurrences as $i => $occurrence) {
            $errors = [...$errors, ...array_map(fn ($e) => $e->nest((string) $i), $occurrence->validateForSubject($subjects[$this->accession]))];
        }

        return array_map(fn ($e) => $e->nest('occurrences'), $errors);
    }
}
