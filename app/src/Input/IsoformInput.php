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

    public static function factory(IsoformCache $cache, string $query): callable
    {
        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));

        $factory = fn ($accession, $occurrences) => self::from($cache, $query, $accession, ...array_values($occurrences));

        return new Validation($factory,
            Field::required('accession', $is_str)->focus(),
            Field::required('occurrences', $is_arr, Map::merged($is_arr))->focus(),
        );
    }

    public static function from(IsoformCache $cache, string $query, string $accession, array ...$occurrences): self
    {
        $input = new self($accession, ...$occurrences);

        // validate the accession.
        $errors = array_map(fn ($e) => $e->nest('accession'), $input->validateAccession($cache));

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        // validate the occurrences.
        $errors = array_map(fn ($e) => $e->nest('occurrences'), $input->validateOccurrencesCount());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $errors = array_map(fn ($e) => $e->nest('occurrences'), $input->validateOccurrences($cache, $query));

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $errors = array_map(fn ($e) => $e->nest('occurrences'), $input->validateOccurrencesUniqueness());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
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

    private function validateAccession(IsoformCache $cache): array
    {
        $sequence = $cache->sequence($this->accession);

        return !$sequence
            ? [new Error(sprintf('is not associated to this protein'))]
            : [];
    }

    private function validateOccurrencesCount(): array
    {
        return count($this->occurrences) == 0
            ? [new Error('must not be empty')]
            : [];
    }

    private function validateOccurrences(IsoformCache $cache, string $query): array
    {
        $sequence = $cache->sequence($this->accession);

        if (!$sequence) return [];

        $are_occurrences = Map::merged(OccurrenceInput::factory($sequence, $query));

        try {
            $are_occurrences($this->occurrences);
        }

        catch (InvalidDataException $e) {
            return $e->errors();
        }

        return [];
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
