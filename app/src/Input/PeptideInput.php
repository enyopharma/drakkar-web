<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

use App\Assertions\ProteinType;

final class PeptideInput
{
    const MIN_LENGTH = 5;
    const MAX_LENGTH = 20;
    const SEQUENCE_PATTERN = '/^[A-Z]*$/';

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $data = (array) $request->getParsedBody();

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        $errors = [];

        if (!array_key_exists('type', $data)) $errors[] = Error::nested('type', 'is required');
        if (!array_key_exists('sequence', $data)) $errors[] = Error::nested('sequence', 'is required');
        if (!array_key_exists('cter', $data)) $errors[] = Error::nested('cter', 'is required');
        if (!array_key_exists('nter', $data)) $errors[] = Error::nested('nter', 'is required');
        if (!array_key_exists('affinity', $data)) $errors[] = Error::nested('affinity', 'is required');
        if (!array_key_exists('hotspots', $data)) $errors[] = Error::nested('hotspots', 'is required');
        if (!array_key_exists('methods', $data)) $errors[] = Error::nested('methods', 'is required');
        if (!array_key_exists('info', $data)) $errors[] = Error::nested('info', 'is required');

        if (!is_string($data['type'] ?? '')) $errors[] = Error::nested('type', 'must be a string');
        if (!is_string($data['sequence'] ?? '')) $errors[] = Error::nested('sequence', 'must be a string');
        if (!is_string($data['cter'] ?? '')) $errors[] = Error::nested('cter', 'must be a string');
        if (!is_string($data['nter'] ?? '')) $errors[] = Error::nested('nter', 'must be a string');
        if (!is_array($data['affinity'] ?? [])) $errors[] = Error::nested('affinity', 'must be an array');
        if (!is_array($data['hotspots'] ?? [])) $errors[] = Error::nested('hotspots', 'must be an array');
        if (!is_array($data['methods'] ?? [])) $errors[] = Error::nested('methods', 'must be an array');
        if (!is_string($data['info'] ?? '')) $errors[] = Error::nested('info', 'must be a string');

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        $type = $data['type'];
        $sequence = $data['sequence'];
        $cter = $data['cter'];
        $nter = $data['nter'];
        $affinity = $data['affinity'];
        $hotspots = $data['hotspots'];
        $methods = $data['methods'];
        $info = $data['info'];

        return self::from($type, $sequence, $cter, $nter, $affinity, $hotspots, $methods, $info);
    }

    public static function from(
        string $type,
        string $sequence,
        string $cter,
        string $nter,
        array $affinity,
        array $hotspots,
        array $methods,
        string $info,
    ): self {
        $input = new self($type, $sequence, $cter, $nter, $affinity, $hotspots, $methods, $info);

        $errors = [
            ...$input->validateType(),
            ...$input->validateSequence(),
            ...$input->validateAffinity(),
            ...$input->validateHotspots(),
            ...$input->validateMethods(),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        public readonly string $type,
        public readonly string $sequence,
        public readonly string $cter,
        public readonly string $nter,
        public readonly array $affinity,
        public readonly array $hotspots,
        public readonly array $methods,
        public readonly string $info,
    ) {
    }

    public function type(): string
    {
        return $this->type;
    }

    public function sequence(): string
    {
        return $this->sequence;
    }

    public function data(): array
    {
        return [
            'cter' => $this->cter,
            'nter' => $this->nter,
            'affinity' => $this->affinity,
            'hotspots' => $this->hotspots,
            'methods' => $this->methods,
            'info' => $this->info,
        ];
    }

    private function validateType(): array
    {
        $errors = [];

        if (!ProteinType::isValid($this->type)) {
            $errors[] = Error::nested('type', vsprintf('must be either \'%s\' or \'%s\'', [
                ProteinType::H,
                ProteinType::V,
            ]));
        }

        return $errors;
    }

    private function validateSequence(): array
    {
        $errors = [];

        if (strlen($this->sequence) < self::MIN_LENGTH) {
            $errors[] = Error::nested('sequence', sprintf('Must be longer than or equal to %s', self::MIN_LENGTH));
        }

        if (strlen($this->sequence) > self::MAX_LENGTH) {
            $errors[] = Error::nested('sequence', sprintf('Must be shorter than or equal to %s', self::MAX_LENGTH));
        }

        if (preg_match(self::SEQUENCE_PATTERN, $this->sequence) === 0) {
            $errors[] = Error::nested('sequence', sprintf('must match %s', self::SEQUENCE_PATTERN));
        }

        return $errors;
    }

    private function validateAffinity(): array
    {
        $errors = [];

        if (!array_key_exists('type', $this->affinity)) {
            $errors[] = Error::nested('type', 'is required')->nest('affinity');
        }

        if (!array_key_exists('value', $this->affinity)) {
            $errors[] = Error::nested('value', 'is required')->nest('affinity');
        }

        if (!array_key_exists('unit', $this->affinity)) {
            $errors[] = Error::nested('unit', 'is required')->nest('affinity');
        }

        if (!is_string($this->affinity['type'] ?? '')) {
            $errors[] = Error::nested('type', 'must be a string')->nest('affinity');
        }

        if (!is_null($this->affinity['value'] ?? null) && !is_int($this->affinity['value'] ?? 0) && !is_float($this->affinity['value'] ?? 0.0)) {
            $errors[] = Error::nested('type', 'must be a number or null')->nest('affinity');
        }

        if (!is_string($this->affinity['unit'] ?? '')) {
            $errors[] = Error::nested('unit', 'must be a string')->nest('affinity');
        }

        return $errors;
    }

    private function validateHotspots(): array
    {
        $errors = [];

        $no_num = false;
        $no_str = false;
        $too_big = false;

        foreach ($this->hotspots as $key => $value) {
            if (!is_int($key)) $no_num = true;
            if (!is_string($value)) $no_str = true;
            if (is_int($key) && $key >= strlen($this->sequence)) $too_big = true;
        }

        if ($no_num) $errors[] = Error::nested('hotspots', 'positions must be numeric');
        if ($no_str) $errors[] = Error::nested('hotspots', 'descriptions must be string');
        if ($too_big) $errors[] = Error::nested('hotspots', 'position cant be outsite sequence');

        return $errors;
    }

    private function validateMethods(): array
    {
        $errors = [];

        if (!array_key_exists('expression', $this->methods)) {
            $errors[] = Error::nested('expression', 'is required')->nest('methods');
        }

        if (!array_key_exists('interaction', $this->methods)) {
            $errors[] = Error::nested('interaction', 'is required')->nest('methods');
        }

        if (!is_string($this->methods['expression'] ?? '')) {
            $errors[] = Error::nested('expression', 'must be a string')->nest('methods');
        }

        if (!is_string($this->methods['interaction'] ?? '')) {
            $errors[] = Error::nested('interaction', 'must be a string')->nest('methods');
        }

        return $errors;
    }
}
