<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use Quanta\Validation\Types\AbstractStringMatching;

final class Sequence extends AbstractStringMatching
{
    protected static function pattern(): string
    {
        return '/^[A-Z]{5,20}$/';
    }
}
