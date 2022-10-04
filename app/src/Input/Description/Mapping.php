<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Types\AbstractStringMatching;

final class Mapping extends AbstractStringMatching
{
    protected static function pattern(): string
    {
        return '/^[A-Z]{4,}$/';
    }
}
