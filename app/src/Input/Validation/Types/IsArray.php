<?php

declare(strict_types=1);

namespace App\Input\Validation\Types;

use App\Input\Validation\Result;

final class IsArray
{
    public function __invoke(mixed $value): Result
    {
        if (is_array($value)) {
            return Result::success($value);
        }

        return Result::error('%%s must be an array');
    }
}
