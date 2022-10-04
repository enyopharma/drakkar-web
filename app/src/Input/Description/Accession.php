<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation\Types\AbstractStringMatching;

final class Accession extends AbstractStringMatching
{
    protected static function pattern(): string
    {
        return '/^[A-Z0-9]{6,10}(-[0-9]+)?$/';
    }
}
