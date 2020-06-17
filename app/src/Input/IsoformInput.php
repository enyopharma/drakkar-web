<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Bound;
use Quanta\Validation\Field;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\NotEmpty;
use Quanta\Validation\Rules\Matching;
use Quanta\Validation\Rules\GreaterThanEqual;

final class IsoformInput
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]+(-[0-9]+)?$/';

    private string $accession;

    private array $occurrences;

    public static function factory(DataSource $source): callable
    {
        $factory = function (string $accession, array $occurrences) {
            return new self($accession, ...array_values($occurrences));
        };

        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));
        $is_gte1 = new Guard(new GreaterThanEqual(1));
        $is_not_empty = new Guard(new NotEmpty);
        $is_accession = new Guard(new Matching(self::ACCESSION_PATTERN));
        $is_occurrence = OccurrenceInput::factory();
        $is_existing = new Guard(fn ($x) => $x->isExisting($source));
        $are_occurrences_valid = new Guard(fn ($x) => $x->areOccurrencesValid($source));

        $validation = new Validation($factory,
            Field::required('accession', $is_str, $is_not_empty, $is_accession)->focus(),
            Field::required('occurrences', $is_arr, $is_gte1, Map::merged($is_arr, $is_occurrence))->focus(),
        );

        return new Bound($validation, $is_existing, $are_occurrences_valid);
    }

    private function __construct(
        string $accession,
        OccurrenceInput $occurrence,
        OccurrenceInput ...$occurrences
    ) {
        $this->accession = $accession;
        $this->occurrences = [$occurrence, ...$occurrences];
    }

    private function isExisting(DataSource $source): array
    {
        $data = $source->isoform($this->accession);

        if ($data) {
            return [];
        }

        return [
            new Error(sprintf('no isoform with accession [%s]', $this->accession)),
        ];
    }

    private function areOccurrencesValid(DataSource $source): array
    {
        $data = $source->isoform($this->accession);

        if (!$data) return [];

        $length = strlen($data['sequence']);

        $errors = [];

        foreach ($this->occurrences as $i => $occurrence) {
            if ($occurrence->stop() > $length) {
                $errors[] = new Error(sprintf('occurrence [%s] must be inside the isoform', $i));
            }
        }

        return $errors;
    }

    public function accession(): string
    {
        return $this->accession;
    }

    public function occurrences(): array
    {
        return $this->occurrences;
    }

    public function data(): array
    {
        return [
            'accession' => $this->accession,
            'occurrences' => array_map(fn ($x) => $x->data(), $this->occurrences),
        ];
    }
}
