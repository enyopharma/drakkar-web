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
    private string $accession;

    private array $occurrences;

    public static function factory(array $sequences, string $query): callable
    {
        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));

        $factory = fn ($accession, $occurrences) => self::from($sequences, $query, $accession, ...array_values($occurrences));

        return new Validation($factory,
            Field::required('accession', $is_str)->focus(),
            Field::required('occurrences', $is_arr, Map::merged($is_arr))->focus(),
        );
    }

    public static function from(array $sequences, string $query, string $accession, array ...$occurrences): self
    {
        $input = new self($accession, ...$occurrences);

        return validated($input, ...$input->validate($sequences, $query));
    }

    private function __construct(string $accession, array ...$occurrences)
    {
        $this->accession = $accession;
        $this->occurrences = $occurrences;
    }

    public function data(): array
    {
        return [
            'accession' => $this->accession,
            'occurrences' => $this->occurrences,
        ];
    }

    private function validate(array $sequences, string $query): array
    {
        return bound(
            nested('accession', ...$this->validateAccession($sequences)),
            nested('occurrences', ...$this->validateOccurrencesCount()),
            nested('occurrences', ...$this->validateOccurrences($sequences, $query)),
            nested('occurrences', ...$this->validateOccurrencesUniqueness()),
        );
    }

    private function validateAccession(array $sequences): array
    {
        return !array_key_exists($this->accession, $sequences)
            ? [new Error(sprintf('is not associated to this protein'))]
            : [];
    }

    private function validateOccurrencesCount(): array
    {
        return count($this->occurrences) == 0
            ? [new Error('must not be empty')]
            : [];
    }

    private function validateOccurrences(array $sequences, string $query): array
    {
        $sequence = $sequences[$this->accession] ?? null;

        if (is_null($sequence)) return [];

        $are_occurrences = Map::merged(OccurrenceInput::factory($sequence, $query));

        return unpacked(fn () => $are_occurrences($this->occurrences));
    }

    private function validateOccurrencesUniqueness(): array
    {
        $errors = [];

        $seen = [];

        foreach ($this->occurrences as ['start' => $start, 'stop' => $stop]) {
            $nb = $seen[$start][$stop] ?? 0;

            if ($nb == 1) {
                $errors[] = new Error(sprintf('[%s, %s] must be present only once', $start, $stop));
            }

            $seen[$start][$stop] = $nb + 1;
        }

        return $errors;
    }
}
