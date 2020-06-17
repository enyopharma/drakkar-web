<?php

declare(strict_types=1);

namespace App\Actions;

final class PopulateRunResult
{
    const SUCCESS = 0;
    const NOT_FOUND = 1;
    const ALREADY_POPULATED = 2;
    const FAILURE = 3;

    private int $state;

    private string $name;

    public static function success(string $name): self
    {
        return new self(self::SUCCESS, $name);
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    public static function alreadyPopulated(string $name): self
    {
        return new self(self::ALREADY_POPULATED, $name);
    }

    public static function failure(string $name): self
    {
        return new self(self::FAILURE, $name);
    }

    private function __construct(int $state, string $name = '')
    {
        $this->state = $state;
        $this->name = $name;
    }

    public function isSuccess(): bool
    {
        return $this->state == self::SUCCESS;
    }

    /**
     * @return mixed
     */
    public function match(array $alternatives)
    {
        $all = [self::SUCCESS, self::NOT_FOUND, self::ALREADY_POPULATED, self::FAILURE];

        $keys = array_keys($alternatives);

        if (count(array_diff($all, $keys)) > 0) {
            throw new \InvalidArgumentException('missing alternatives');
        }

        if (! is_callable($alternative = $alternatives[$this->state])) {
            throw new \InvalidArgumentException('alternative must be a callable');
        }

        return $alternative($this->name);
    }
}
