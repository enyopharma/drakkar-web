<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class AlignmentInput
{
    const MIN_LENGTH = 4;

    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    public static function fromArray(array $data): self
    {
        $errors = [];

        if (!array_key_exists('sequence', $data)) $errors[] = Error::nested('sequence', 'is required');
        if (!array_key_exists('isoforms', $data)) $errors[] = Error::nested('isoforms', 'is required');

        if (!is_string($data['sequence'] ?? '')) $errors[] = Error::nested('sequence', 'must be a string');
        if (!is_array($data['isoforms'] ?? [])) $errors[] = Error::nested('isoforms', 'must be an array');

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $sequence = $data['sequence'];
        $isoforms = $data['isoforms'];

        return self::from($sequence, ...$isoforms);
    }

    public static function from(string $sequence, array ...$isoforms): self
    {
        $input = new self($sequence, ...$isoforms);

        $errors = [];

        array_push($errors, ...$input->validateSequence());
        array_push($errors, ...$input->validateIsoforms());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    public readonly array $isoforms;

    private function __construct(public readonly string $sequence, array ...$isoforms)
    {
        $this->isoforms = $isoforms;
    }

    private function validateSequence(): array
    {
        $errors = [];

        if (strlen($this->sequence) < self::MIN_LENGTH) {
            $errors[] = Error::nested('sequence', sprintf('must be longer than %s', self::MIN_LENGTH - 1));
        }

        if (preg_match(self::SEQUENCE_PATTERN, $this->sequence) === 0) {
            $errors[] = Error::nested('sequence', sprintf('must match %s', self::SEQUENCE_PATTERN));
        }

        return $errors;
    }

    private function validateIsoforms(): array
    {
        $nested = [];

        $accessions = [];

        foreach ($this->isoforms as $i => $isoform) {
            try {
                $input = IsoformInput::fromArray($isoform);

                $accessions[] = $input->accession;

                foreach ($input->occurrences as $o => $occurrence) {
                    ['start' => $start, 'stop' => $stop] = $occurrence;

                    if ($stop - $start + 1 > $this->sequence) {
                        $nested[] = (new Error('must be greater than or equal to sequence length'))
                            ->nest('isoforms', (string) $i, 'occurrences', (string) $o);
                    }
                }
            } catch (InvalidDataException $e) {
                array_push($nested, ...$e->nest('isoforms', (string) $i)->errors());
            }
        }

        $errors = [];

        if (count($this->isoforms) == 0) {
            $errors[] = Error::nested('isoforms', 'must not be empty');
        }

        if (count($accessions) > count(array_unique($accessions))) {
            $errors[] = Error::nested('isoforms', 'accessions must be unique');
        }

        return [...$errors, ...$nested];
    }
}
