<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayShape;
use Quanta\Validation\Rules\IsGreaterThan;

final class IsCoordinates
{
    public function __invoke(array $data): InputInterface
    {
        return Input::unit($data)->bind(
            fn ($x) => $this->makeCoordinates($x),
            fn ($x) => $this->validateCoordinates($x),
        );
    }

    private function makeCoordinates(array $data): InputInterface
    {
        $isint = new HasType('integer');
        $ispos = new IsGreaterThan(0);

        $makeCoordinates = new ArrayShape([
            'start' => [$isint, $ispos],
            'stop' => [$isint, $ispos],
        ]);

        return $makeCoordinates($data);
    }

    private function validateCoordinates(array $coordinates): InputInterface
    {
        return $coordinates['start'] <= $coordinates['stop']
            ? Input::unit($coordinates)
            : new Failure(new Error('start must be less than stop'));
    }
}
