<?php

namespace App\Routing;

final class Methods
{
    const ALLOWED = ['GET', 'POST', 'PUT', 'DELETE'];

    public static function from(string $value, string ...$values): self
    {
        $values = [$value, ...$values];

        if (count(array_intersect($values, self::ALLOWED)) < count($values)) {
            throw new \InvalidArgumentException('methods array must contain only allowed http methods');
        }

        return new self($values);
    }

    private function __construct(private array $values) {}

    public function values(): array
    {
        return $this->values;
    }
}
