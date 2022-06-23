<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute]
final class Method
{
    public readonly array $values;

    public function __construct(string $value, string ...$values)
    {
        $this->values = [$value, ...$values];
    }
}
