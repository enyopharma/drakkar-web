<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class IsoformInput
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';

    public static function fromArray(array $data): self
    {
        $errors = [];

        if (!array_key_exists('accession', $data)) $errors[] = Error::nested('accession', 'is required');
        if (!array_key_exists('occurrences', $data)) $errors[] = Error::nested('occurrences', 'is required');

        if (!is_string($data['accession'] ?? '')) $errors[] = Error::nested('accession', 'must be a string');
        if (!is_array($data['occurrences'] ?? [])) $errors[] = Error::nested('occurrences', 'must be an array');

        $accession = $data['accession'];
        $occurrences = [];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        foreach ($data['occurrences'] as $o => $occurrence) {
            try {
                $occurrences[] = OccurrenceInput::fromArray($occurrence);
            } catch (InvalidDataException $e) {
                $es = array_map(fn () => $e->nest('occurrences', (string) $o), $e->errors());

                array_push($errors, ...$es);
            }
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return self::from($accession, ...$occurrences);
    }

    public static function from(string $accession, OccurrenceInput ...$occurrences): self
    {
        $input = new self($accession, ...$occurrences);

        $errors = [];

        array_push($errors, ...$input->validateAccession());
        array_push($errors, ...$input->validateOccurrences());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    public readonly array $occurrences;

    private function __construct(public readonly string $accession, OccurrenceInput ...$occurrences)
    {
        $this->occurrences = $occurrences;
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
            ? [Error::nested('accession', sprintf('must match %s', self::ACCESSION_PATTERN))]
            : [];
    }

    private function validateOccurrences(): array
    {
        $errors = [];

        if (count($this->occurrences) == 0) {
            $errors[] = Error::nested('occurrences', 'must not be empty');
        }

        $seen = [];

        foreach ($this->occurrences as $occ) {
            $nb = $seen[$occ->start][$occ->stop] ?? 0;

            if ($nb == 1) {
                $errors[] = Error::nested('occurrences', sprintf('[%s, %s] must be present only once', $occ->start, $occ->stop));
            }

            $seen[$occ->start][$occ->stop] = $nb + 1;
        }

        return $errors;
    }
}
