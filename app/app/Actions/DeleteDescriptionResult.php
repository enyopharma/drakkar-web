<?php

declare(strict_types=1);

namespace App\Actions;

final class DeleteDescriptionResult
{
    const SUCCESS = 0;
    const NOT_FOUND = 1;

    private int $state;

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    private function __construct(int $state)
    {
        $this->state = $state;
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
        $all = [self::SUCCESS, self::NOT_FOUND];

        $keys = array_keys($alternatives);

        if (count(array_diff($all, $keys)) > 0) {
            throw new \InvalidArgumentException('missing alternatives');
        }

        if (! is_callable($alternative = $alternatives[$this->state])) {
            throw new \InvalidArgumentException('alternative must be a callable');
        }

        return $alternative();
    }
}
