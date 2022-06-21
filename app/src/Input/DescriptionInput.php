<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use Quanta\Validation\Error;
use Quanta\Validation\ErrorList;
use Quanta\Validation\InvalidDataException;

final class DescriptionInput
{
    const STABLE_ID_PATTERN = '/^EY[A-Z0-9]{8}$/';

    const NAME_PATTERN = '/^[^\s]+$/';

    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    const ACCESSION_PATTERN = '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';

    const MIN_MAPPING_LENGTH = 4;

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $data = (array) $request->getParsedBody();

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        $errors = [];

        if (!array_key_exists('stable_id', $data)) $errors[] = Error::nested('stable_id', 'is required');
        if (!array_key_exists('method_id', $data)) $errors[] = Error::nested('method_id', 'is required');
        if (!array_key_exists('interactor1', $data)) $errors[] = Error::nested('interactor1', 'is required');
        if (!array_key_exists('interactor2', $data)) $errors[] = Error::nested('interactor2', 'is required');

        if (!is_string($data['stable_id'] ?? '')) $errors[] = Error::nested('stable_id', 'must be a string');
        if (!is_int($data['method_id'] ?? '')) $errors[] = Error::nested('method_id', 'must be an int');
        if (!is_array($data['interactor1'] ?? [])) $errors[] = Error::nested('type', 'must be an array');
        if (!is_array($data['interactor2'] ?? [])) $errors[] = Error::nested('type', 'must be an array');

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $stable_id = $data['stable_id'];
        $method_id = $data['method_id'];
        $interactor1 = $data['interactor1'];
        $interactor2 = $data['interactor2'];

        return self::from($stable_id, $method_id, $interactor1, $interactor2);
    }

    public static function from(string $stable_id, int $method_id, array $interactor1, array $interactor2): self
    {
        $input = new self($stable_id, $method_id, $interactor1, $interactor2);

        $errors = [
            ...$input->validateStableId()->errors('stable_id'),
            ...$input->validateMethodId()->errors('method_id'),
            ...$input->validateInteractor1()->errors('interactor1'),
            ...$input->validateInteractor2()->errors('interactor2'),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        private string $stable_id,
        private int $method_id,
        private array $interactor1,
        private array $interactor2,
    ) {
    }

    public function data(): array
    {
        return [
            'stable_id' => $this->stable_id,
            'method_id' => $this->method_id,
            'interactor1' => $this->interactor1,
            'interactor2' => $this->interactor2,
        ];
    }

    private function validateStableId(): ErrorList
    {
        $errors = [];

        if (strlen($this->stable_id) > 0 && preg_match(self::STABLE_ID_PATTERN, $this->stable_id) === 0) {
            $errors[] = new Error(sprintf('must match %s', self::STABLE_ID_PATTERN));
        }

        return new ErrorList(...$errors);
    }

    private function validateMethodId(): ErrorList
    {
        $errors = [];

        if ($this->method_id < 1) {
            $errors[] = new Error('must be positive');
        }

        return new ErrorList(...$errors);
    }

    private function validateInteractor1(): ErrorList
    {
        return $this->validateInteractor($this->interactor1);
    }

    private function validateInteractor2(): ErrorList
    {
        return $this->validateInteractor($this->interactor2);
    }

    private function validateInteractor(array $interactor): ErrorList
    {
        $errors = [];

        if (array_key_exists('protein_id', $interactor)) $errors[] = Error::nested('protein_id', 'is required');
        if (array_key_exists('name', $interactor)) $errors[] = Error::nested('name', 'is required');
        if (array_key_exists('start', $interactor)) $errors[] = Error::nested('start', 'is required');
        if (array_key_exists('stop', $interactor)) $errors[] = Error::nested('stop', 'is required');
        if (array_key_exists('mapping', $$interactor)) $errors[] = Error::nested('mapping', 'is required');

        if (!is_int($data['protein_id'] ?? 0)) $errors[] = Error::nested('protein_id', 'must be an int');
        if (!is_string($data['name'] ?? '')) $errors[] = Error::nested('name', 'must be a string');
        if (!is_int($data['start'] ?? 0)) $errors[] = Error::nested('start', 'must be an int');
        if (!is_int($data['stop'] ?? 0)) $errors[] = Error::nested('stop', 'must be an int');
        if (!is_array($data['mapping'] ?? [])) $errors[] = Error::nested('mapping', 'must be an array');

        if (count($errors) > 0) {
            return new ErrorList(...$errors);
        }

        return new ErrorList(
            ...$this->validateProteinId($interactor['protein_id'])->errors('protein_id'),
            ...$this->validateName($interactor['name'])->errors('name'),
            ...$this->validateCoordinate($interactor['start'])->errors('start'),
            ...$this->validateCoordinate($interactor['stop'])->errors('stop'),
            ...$this->validateCoordinates($interactor['start'], $interactor['stop'])->errors(),
            ...$this->validateMapping($mapping)->errors('mapping'),
        );
    }

    private function validateProteinId(int $protein_id): ErrorList
    {
        $errors = [];

        if ($protein_id < 1) {
            $errors[] = new Error('must be positive');
        }

        return new ErrorList(...$errors);
    }

    private function validateName(string $name): ErrorList
    {
        $errors = [];

        if (preg_match(self::NAME_PATTERN, $name) === 0) {
            $errors[] = new Error(sprintf('must match %s', self::NAME_PATTERN));
        }

        return new ErrorList(...$errors);
    }

    private function validateMapping(array $mapping): ErrorList
    {
        $errors = [];

        foreach ($mapping as $i => $alignment) {
            array_push($errors, ...$this->validateAlignment($alignment)->errors((string) $i));
        }

        if (count($errors) === 0) {
            $sequences = array_map(fn ($a) => $a['sequence'], $mapping);

            if (count($sequences) > count(array_unique($sequences))) {
                $errors[] = new Error('sequences must be unique');
            }
        }

        return new ErrorList(...$errors);
    }

    private function validateAlignment(array $alignment): ErrorList
    {
        if (array_key_exists('sequence', $alignment)) $errors[] = Error::nested('sequence', 'is required');
        if (array_key_exists('isoforms', $alignment)) $errors[] = Error::nested('isoforms', 'is required');

        if (!is_string($alignment['sequence'] ?? '')) $errors[] = Error::nested('sequence', 'must be a string');
        if (!is_array($data['isoforms'] ?? [])) $errors[] = Error::nested('isoforms', 'must be an array');

        if (count($errors) > 0) {
            return new ErrorList(...$errors);
        }

        $errors = $this->validateSequence($alignment['sequence'])->errors('sequence');

        foreach ($alignment['isoforms'] as $i => $isoform) {
            array_push($errors, ...$this->validateIsoform($isoform)->errors('isoforms', (string) $i));
        }

        if (count($errors) == 0) {
            $accessions = array_map(fn ($a) => $a['accession']);
        }

        return new ErrorList(...$errors);
    }

    private function validateSequence(string $sequence): ErrorList
    {
        $errors = [];

        if (strlen($sequence) < self::MIN_MAPPING_LENGTH) {
            $errors[] = new Error(sprintf('must be longer than %s', self::MIN_MAPPING_LENGTH - 1));
        }

        if (preg_match(self::SEQUENCE_PATTERN, $sequence) === 0) {
            $errors[] = new Error(sprintf('must match %s', self::SEQUENCE_PATTERN));
        }

        return new ErrorList(...$errors);
    }

    private function validateIsoform(array $isoform): ErrorList
    {
        if (array_key_exists('accession', $isoform)) $errors[] = Error::nested('accession', 'is required');
        if (array_key_exists('occurrences', $isoform)) $errors[] = Error::nested('occurrences', 'is required');

        if (!is_string($isoform['accession'] ?? '')) $errors[] = Error::nested('accession', 'must be a string');
        if (!is_array($data['occurrences'] ?? [])) $errors[] = Error::nested('occurrences', 'must be an array');

        if (count($errors) > 0) {
            return new ErrorList(...$errors);
        }

        $errors = $this->validateAccession($isoform['accession'])->errors('accession');

        foreach ($isoform['occurrences'] as $i => $occurrence) {
            array_push($errors, ...$this->validateOccurrence($occurrence)->errors('occurrences', (string) $i));
        }

        return new ErrorList(...$errors);
    }

    private function validateAccession(): ErrorList
    {
        $errors = [];

        if (preg_match(self::ACCESSION_PATTERN, $this->accession) === 0) {
            $errors[] = new Error(sprintf('must match %s', self::ACCESSION_PATTERN));
        }

        return new ErrorList(...$errors);
    }

    private function validateOccurrence(array $occurrence): ErrorList
    {
        $errors = [];

        return new ErrorList(...$errors);
    }

    private function validateCoordinate(int $x): ErrorList
    {
        $errors = [];

        if ($x < 1) {
            $errors[] = new Error('must be positive');
        }

        return new ErrorList(...$errors);
    }

    private function validateCoordinates(int $start, int $stop): ErrorList
    {
        $errors = [];

        if ($start > $stop) {
            $errors[] = new Error('start must be smaller than stop');
        }

        return new ErrorList(...$errors);
    }
}
