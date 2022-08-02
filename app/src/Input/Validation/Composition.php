<?php

declare(strict_types=1);

namespace App\Input\Validation;

final class Composition
{
    public static function from(callable ...$fs): self
    {
        return new self(...$fs);
    }

    private array $fs;

    private function __construct(callable ...$fs)
    {
        $this->fs = $fs;
    }

    public function reduce(mixed $init): mixed
    {
        return array_reduce($this->fs, fn ($x, $f) => $f($x), $init);
    }
}
