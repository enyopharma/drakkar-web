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

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $accession = $data['accession'];
        $occurrences = $data['occurrences'];

        return self::from($accession, ...$occurrences);
    }

    public static function from(string $accession, array ...$occurrences): self
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

    private function __construct(public readonly string $accession, array ...$occurrences)
    {
        $this->occurrences = $occurrences;
    }

    private function validateAccession(): array
    {
        return preg_match(self::ACCESSION_PATTERN, $this->accession) === 0
            ? [Error::nested('accession', sprintf('must match %s', self::ACCESSION_PATTERN))]
            : [];
    }

    private function validateOccurrences(): array
    {
        $nested = [];

        $coordinates = [];

        foreach ($this->occurrences as $o => $occurrence) {
            try {
                $input = OccurrenceInput::fromArray($occurrence);

                $coordinates[] = implode(':', [$input->start, $input->stop]);
            } catch (InvalidDataException $e) {
                array_push($nested, ...$e->nest('occurrences', (string) $o)->errors());
            }
        }

        $errors = [];

        if (count($this->occurrences) == 0) {
            $errors[] = Error::nested('occurrences', 'must not be empty');
        }

        if (count($coordinates) > count(array_unique($coordinates))) {
            $errors[] = Error::nested('occurrences', 'coordinates must be unique');
        }

        return [...$errors, ...$nested];
    }
}
