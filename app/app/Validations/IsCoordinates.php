<?php

declare(strict_types=1);

namespace App\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\Bound;
use Quanta\Validation\Merged;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\GreaterThan;

final class IsCoordinates
{
    public function __invoke(array $data): InputInterface
    {
        $makeCoordinates = \Closure::fromCallable([$this, 'makeCoordinates']);
        $startIsLessThanStop = \Closure::fromCallable([$this, 'startIsLessThanStop']);

        $isStartLessThanStop = new Is($startIsLessThanStop);

        $validate = new Bound($makeCoordinates, $isStartLessThanStop);

        return $validate($data);
    }

    private function makeCoordinates(array $data): InputInterface
    {
        $isInt = new Is(new OfType('integer'));
        $isPos = new Is(new GreaterThan(0));

        $validate = new Merged(
            Field::required('start', $isInt, $isPos),
            Field::required('stop', $isInt, $isPos),
        );

        return $validate($data);
    }

    private function startIsLessThanStop(array $coordinates): array
    {
        return $coordinates['start'] <= $coordinates['stop'] ? [] : [
            new Error('start must be less than stop'),
        ];
    }
}
