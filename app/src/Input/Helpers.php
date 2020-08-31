<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

/**
 * @param T $value
 * @return T
 */
function validated($value, Error ...$errors) {
    if (count($errors) > 0) {
        throw new InvalidDataException(...$errors);
    }

    return $value;
}

function nested (string $namespace, Error ...$errors): array {
    return array_map(fn ($e) => $e->nest($namespace), $errors);
}

function bound(array ...$results): array {
    foreach ($results as $errors) {
        if (count($errors) > 0) {
            return $errors;
        }
    }

    return [];
}

function unpacked(callable $f): array {
    try {
        $f();
    }

    catch (InvalidDataException $e) {
        return $e->errors();
    }

    return [];
}
