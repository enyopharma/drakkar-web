<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;
use Quanta\Validation\InvalidDataException;

final class Coordinates extends AbstractInput
{
    const MIN_LENGTH = 4;

    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('start')->int(Position::class),
            $v->key('stop')->int(Position::class),
        );
    }

    public function __construct(public readonly Position $start, public readonly Position $stop)
    {
        if ($start->value() > $stop->value()) {
            throw new InvalidDataException(
                Error::from('start must be smaller than stop'),
            );
        }

        if ($this->stop->value() - $this->start->value() + 1 < self::MIN_LENGTH) {
            throw new InvalidDataException(
                Error::from('length must be at least %s', ['min' => self::MIN_LENGTH]),
            );
        }
    }
}
