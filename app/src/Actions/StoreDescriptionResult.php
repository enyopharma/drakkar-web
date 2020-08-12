<?php

declare(strict_types=1);

namespace App\Actions;

final class StoreDescriptionResult
{
    const SUCCESS = 0;
    const DESCRIPTION_ALREADY_EXISTS = 1;
    const FIRST_VERSION_FAILURE = 2;
    const NEW_VERSION_FAILURE = 3;

    private int $state;

    private array $description;

    private array $errors;

    public static function success(array $description): self
    {
        return new self(self::SUCCESS, $description);
    }

    public static function descriptionAlreadyExists(): self
    {
        return new self(self::DESCRIPTION_ALREADY_EXISTS);
    }

    public static function stableIdFailure(): self
    {
        return new self(self::STABLE_ID_FAILURE);
    }

    private function __construct(int $state, array $description = [], string ...$errors)
    {
        $this->state = $state;
        $this->description = $description;
        $this->errors = $errors;
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
        $all = [
            self::SUCCESS,
            self::DESCRIPTION_ALREADY_EXISTS,
            self::FIRST_VERSION_FAILURE,
            self::NEW_VERSION_FAILURE,
        ];

        $keys = array_keys($alternatives);

        if (count(array_diff($all, $keys)) > 0) {
            throw new \InvalidArgumentException('missing alternatives');
        }

        if (! is_callable($alternative = $alternatives[$this->state])) {
            throw new \InvalidArgumentException('alternative must be a callable');
        }

        return $alternative($this->description, ...$this->errors);
    }
}
