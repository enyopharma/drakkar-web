<?php

declare(strict_types=1);

namespace Domain\Actions;

final class PopulatePublicationResult
{
    const SUCCESS = 0;
    const NOT_FOUND = 1;
    const ALREADY_POPULATED = 2;
    const PARSING_ERROR = 3;

    private $state;

    private $message;

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    public static function alreadyPopulated(): self
    {
        return new self(self::ALREADY_POPULATED);
    }

    public static function parsingError(string $message): self
    {
        return new self(self::PARSING_ERROR, $message);
    }

    private function __construct(int $state, string $message = '')
    {
        $this->state = $state;
        $this->message = $message;
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
        $all = [self::SUCCESS, self::NOT_FOUND, self::ALREADY_POPULATED, self::PARSING_ERROR];

        $keys = array_keys($alternatives);

        if (count(array_diff($all, $keys)) > 0) {
            throw new \InvalidArgumentException('missing alternatives');
        }

        if (! is_callable($alternative = $alternatives[$this->state])) {
            throw new \InvalidArgumentException('alternative must be a callable');
        }

        return $alternative($this->message);
    }
}
