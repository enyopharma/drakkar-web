<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;
use App\Input\Validation\InvalidDataException;

final class Coordinates extends ArrayInput
{
    const MIN_LENGTH = 4;

    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('start')->int([Position::class, 'from']),
            ArrayKey::required('stop')->int([Position::class, 'from']),
        );
    }

    public function __construct(public readonly Position $start, public readonly Position $stop)
    {
        if ($start->value > $stop->value) {
            throw InvalidDataException::error('start must be smaller than stop');
        }

        if ($this->stop->value - $this->start->value + 1 < self::MIN_LENGTH) {
            throw InvalidDataException::error('length must be at least %s', self::MIN_LENGTH);
        }
    }
}
