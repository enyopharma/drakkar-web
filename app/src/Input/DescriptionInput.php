<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\OfType;
use Quanta\Validation\ArrayFactory;
use Quanta\Validation\InvalidDataException;

final class DescriptionInput
{
    const STABLE_ID_PATTERN = '/^EY[A-Z0-9]{8}$/';

    public static function factory(): callable
    {
        $interactor = InteractorInput::factory();

        $is_str = OfType::guard('string');
        $is_int = OfType::guard('int');
        $is_arr = OfType::guard('array');

        return new ArrayFactory(
            [self::class, 'from'],
            Field::required('stable_id', $is_str)->focus(),
            Field::required('method_id', $is_int)->focus(),
            Field::required('interactor1', $is_arr, $interactor)->focus(),
            Field::required('interactor2', $is_arr, $interactor)->focus(),
        );
    }

    public static function from(string $stable_id, int $method_id, InteractorInput $interactor1, InteractorInput $interactor2): self
    {
        $input = new self($stable_id, $method_id, $interactor1, $interactor2);

        $errors = [
            ...$input->validateStableId(),
            ...$input->validateMethodId(),
        ];;

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        private string $stable_id,
        private int $method_id,
        private InteractorInput $interactor1,
        private InteractorInput $interactor2,
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
        return strlen($this->stable_id) > 0 && preg_match(self::STABLE_ID_PATTERN, $this->stable_id) === 0
            ? [Error::nested('stable_id', sprintf('must match %s', self::STABLE_ID_PATTERN))]
            : [];
    }

    private function validateMethodId(): array
    {
        return $this->method_id < 1
            ? [Error::nested('method_id', 'must be positive')]
            : [];
    }
}
