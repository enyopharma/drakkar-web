<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\OfType;
use Quanta\Validation\ErrorList;
use Quanta\Validation\ArrayFactory;
use Quanta\Validation\InvalidDataException;

final class IsoformInput
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';

    private string $accession;

    private array $occurrences;

    public static function factory(): callable
    {
        $factory = fn ($accession, $occurrences) => self::from($accession, ...array_values($occurrences));

        $is_arr = OfType::guard('array');
        $is_str = OfType::guard('string');
        $occurrence = OccurrenceInput::factory();

        return new ArrayFactory($factory,
            Field::required('accession', $is_str)->focus(),
            Field::required('occurrences', $is_arr, Map::merged($is_arr, $occurrence))->focus(),
        );
    }

    public static function from(string $accession, OccurrenceInput ...$occurrences): self
    {
        $input = new self($accession, ...$occurrences);

        $errors = [
            ...$input->validateAccession()->errors('accession'),
            ...$input->validateOccurrences()->errors('occurrences'),
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

    private function validateAccession(): ErrorList
    {
        $errors = preg_match(self::ACCESSION_PATTERN, $this->accession) === 0
            ? [new Error(sprintf('must match %s', self::ACCESSION_PATTERN))]
            : [];

        return new ErrorList(...$errors);
    }

    private function validateOccurrences(): ErrorList
    {
        $errors = [];

        if (count($this->occurrences) == 0) {
            $errors[] = new Error('must not be empty');
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

        return new ErrorList(...$errors);
    }

    public function validateForSequence(string $sequence): ErrorList
    {
        $errors = [];

        foreach ($this->occurrences as $i => $occurrence) {
            $errors = [...$errors, ...$occurrence->validateForSequence($sequence)->errors('occurrences', (string) $i)];
        }

        return new ErrorList(...$errors);
    }

    public function validateForSubjects(array $subjects): ErrorList
    {
        if (!array_key_exists($this->accession, $subjects)) {
            return new ErrorList(Error::nested('accession', 'must be associated with the interactor'));
        }

        $errors = [];

        foreach ($this->occurrences as $i => $occurrence) {
            $errors = [...$errors, ...$occurrence->validateForSubject($subjects[$this->accession])->errors('occurrences', (string) $i)];
        }

        return new ErrorList(...$errors);
    }
}
