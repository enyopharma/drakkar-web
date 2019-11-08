<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsCoordinates
{
    public function __invoke(array $data): InputInterface
    {
        return Input::unit($data)->validate(
            fn ($x) => $this->step1($x),
            fn ($x) => $this->step2($x),
        );
    }

    private function step1(array $data): InputInterface
    {
        $slice = new Slice;
        $isint = new IsTypedAs('integer');
        $ispos = new IsGreaterThan(0);

        $factory = Input::pure(fn (int $start, int $stop) => compact('start', 'stop'));

        $start = $slice($data, 'start')->validate($isint, $ispos);
        $stop = $slice($data, 'stop')->validate($isint, $ispos);

        return $factory($start, $stop);
    }

    private function step2(array $cdx): InputInterface
    {
        return $cdx['start'] <= $cdx['stop']
            ? new Success($cdx)
            : new Failure(new Error('%%s => start must be less than stop'));
    }
}
