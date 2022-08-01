<?php

declare(strict_types=1);

namespace App\Input\Validation;

abstract class VariadicInput
{
    abstract protected static function validation(VariadicFactory $factory): VariadicFactory;

    public static function from(array $data): static
    {
        $factory = VariadicFactory::class(static::class);

        $factory = static::validation($factory);

        return $factory($data);
    }
}
