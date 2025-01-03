<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute]
final class Pattern
{
    public readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
