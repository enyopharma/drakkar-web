<?php

declare(strict_types=1);

namespace App\Input;

use Psr\Http\Message\ServerRequestInterface;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

final class DescriptionInput
{
    const STABLE_ID_PATTERN = '/^EY[A-Z0-9]{8}$/';

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
        if (!is_array($data['interactor1'] ?? [])) $errors[] = Error::nested('interactor1', 'must be an array');
        if (!is_array($data['interactor2'] ?? [])) $errors[] = Error::nested('interactor2', 'must be an array');

        $stable_id = $data['stable_id'];
        $method_id = $data['method_id'];

        try {
            $interactor1 = InteractorInput::fromArray($data['interactor1']);
        } catch (InvalidDataException $e) {
            $es = array_map(fn () => $e->nest('interactor1'), $e->errors());

            array_push($errors, ...$es);
        }

        try {
            $interactor2 = InteractorInput::fromArray($data['interactor2']);
        } catch (InvalidDataException $e) {
            $es = array_map(fn () => $e->nest('interactor2'), $e->errors());

            array_push($errors, ...$es);
        }

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return self::from($stable_id, $method_id, $interactor1, $interactor2);
    }

    public static function from(string $stable_id, int $method_id, InteractorInput $interactor1, InteractorInput $interactor2): self
    {
        $input = new self($stable_id, $method_id, $interactor1, $interactor2);

        $errors = [];

        array_push($errors, ...$input->validateStableId());
        array_push($errors, ...$input->validateMethodId());

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        public readonly string $stable_id,
        public readonly int $method_id,
        public readonly InteractorInput $interactor1,
        public readonly InteractorInput $interactor2,
    ) {
    }

    public function data(): array
    {
        return [
            'stable_id' => $this->stable_id,
            'method_id' => $this->method_id,
            'interactor1' => $this->interactor1->data(),
            'interactor2' => $this->interactor2->data(),
        ];
    }

    private function validateStableId(): array
    {
        if (strlen($this->stable_id) > 0 && preg_match(self::STABLE_ID_PATTERN, $this->stable_id) === 0) {
            return [Error::nested('stable_id', sprintf('must match %s', self::STABLE_ID_PATTERN))];
        }

        return [];
    }

    private function validateMethodId(): array
    {
        if ($this->method_id < 1) {
            return [Error::nested('method_id', 'must be positive')];
        }

        return [];
    }
}
