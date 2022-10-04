<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Types\AbstractStringMatching;

final class StableId extends AbstractStringMatching
{
    protected static function pattern(): string
    {
        return '/^(EY[A-Z0-9]{8}|)$/';
    }
}
