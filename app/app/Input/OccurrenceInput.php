<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Bound;
use Quanta\Validation\Field;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\LessThanEqual;
use Quanta\Validation\Rules\GreaterThanEqual;

final class OccurrenceInput
{
    private int $start;

    private int $stop;

    private float $identity;

    public static function factory(): callable
    {
        $factory = fn (...$xs) => new self(...$xs);

        $is_int = new Guard(new OfType('int'));
        $is_flt = new Guard(new OfType('float'));
        $is_gte1 = new Guard(new GreaterThanEqual(1));
        $is_gte96 = new Guard(new GreaterThanEqual(96));
        $is_gte100 = new Guard(new LessThanEqual(100));
        $are_coordinates_valid = new Guard(fn ($x) => $x->areCoordinatesValid());

        $validation = new Validation($factory,
            Field::required('start', $is_int, $is_gte1)->focus(),
            Field::required('stop', $is_int, $is_gte1)->focus(),
            Field::required('identity', $is_flt, $is_gte96, $is_gte100)->focus(),
        );

        return new Bound($validation, $are_coordinates_valid);
    }

    private function __construct(int $start, int $stop, float $identity)
    {
        $this->start = $start;
        $this->stop = $stop;
        $this->identity = $identity;
    }

    private function areCoordinatesValid(): array
    {
        if ($this->start <= $this->stop) {
            return [];
        }

        return [
            new Error('start must be less than stop'),
        ];
    }

    public function start(): int
    {
        return $this->start;
    }

    public function stop(): int
    {
        return $this->stop;
    }

    public function data(): array
    {
        return [
            'start' => $this->start,
            'stop' => $this->stop,
            'identity' => $this->identity,
        ];
    }
}
