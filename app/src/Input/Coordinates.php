<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayFactory;
use App\Input\Validation\InvalidDataException;

final class Coordinates
{
    const MIN_LENGTH = 4;

    /**
     * @param mixed[] $data
     */
    public static function from(array $data): self
    {
        $factory = ArrayFactory::class(self::class)->validators(
            ArrayKey::required('start')->int([Position::class, 'from']),
            ArrayKey::required('stop')->int([Position::class, 'from']),
        );

        return $factory($data);
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
