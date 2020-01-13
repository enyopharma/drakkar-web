<?php

declare(strict_types=1);

namespace Domain\Actions;

final class UpdatePublicationStateResult
{
    const SUCCESS = 0;
    const NOT_FOUND = 1;
    const NOT_VALID = 2;

    private $state;

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    public static function notValid(): self
    {
        return new self(self::NOT_VALID);
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
        $all = [self::SUCCESS, self::NOT_FOUND, self::NOT_VALID];

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
