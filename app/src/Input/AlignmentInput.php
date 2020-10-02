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

final class AlignmentInput
{
    const MIN_LENGTH = 4;

    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    private string $sequence;

    private array $isoforms;

    public static function factory(): callable
    {
        $factory = fn ($sequence, $isoforms) => self::from($sequence, ...array_values($isoforms));

        $is_arr = OfType::guard('array');
        $is_str = OfType::guard('string');
        $isoform = IsoformInput::factory();

        return new ArrayFactory($factory,
            Field::required('sequence', $is_str)->focus(),
            Field::required('isoforms', $is_arr, Map::merged($is_arr, $isoform))->focus()
        );
    }

    public static function from(string $sequence, IsoformInput ...$isoforms): self
    {
        $input = new self($sequence, ...$isoforms);

        $errors = [
            ...$input->validateSequence(),
            ...$input->validateIsoforms(),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(string $sequence, IsoformInput ...$isoforms)
    {
        $this->sequence = $sequence;
        $this->isoforms = $isoforms;
    }
    
    public function sequence(): string
    {
        return $this->sequence;
    }

    public function data(): array
    {
        return [
            'sequence' => $this->sequence,
            'isoforms' => array_map(fn ($i) => $i->data(), $this->isoforms),
        ];
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
        $errors = [];

        if (count($this->isoforms) == 0) {
            $errors[] = Error::nested('isoforms', 'must not be empty');
        }

        $accessions = array_map(fn ($i) => $i->accession(), $this->isoforms);

        if (count($accessions) > count(array_unique($accessions))) {
            $errors[] = Error::nested('accession', 'must be unique')->nest('isoforms');
        }

        foreach ($this->isoforms as $i => $isoform) {
            $errors = [...$errors, ...$isoform->validateForSequence($this->sequence)->errors('isoforms', (string) $i)];
        }

        return $errors;
    }

    public function validateForSubjects(array $subjects): ErrorList
    {
        $errors = [];

        foreach ($this->isoforms as $i => $isoform) {
            $errors = [...$errors, ...$isoform->validateForSubjects($subjects)->errors('isoforms', (string) $i)];
        }

        return new ErrorList(...$errors);
    }
}
