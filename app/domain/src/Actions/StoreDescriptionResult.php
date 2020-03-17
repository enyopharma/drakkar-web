<?php

declare(strict_types=1);

namespace Domain\Actions;

final class StoreDescriptionResult
{
    const SUCCESS = 0;
    const DESCRIPTION_ALREADY_EXISTS = 1;
    const STABLE_ID_FAILURE = 2;

    private $state;

    private $description;

    private $errors;

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
            self::STABLE_ID_FAILURE,
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
