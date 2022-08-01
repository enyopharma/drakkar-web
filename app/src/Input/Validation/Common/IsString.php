<?php

declare(strict_types=1);

namespace App\Input\Validation\Common;

use App\Input\Validation\Result;

final class IsString
{
    public function __invoke(mixed $value): Result
    {
        if (is_string($value)) {
            return Result::success($value);
        }

        return Result::error('%%s must be a string');
    }
}
