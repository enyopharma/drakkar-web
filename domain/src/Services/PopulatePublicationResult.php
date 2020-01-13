<?php

declare(strict_types=1);

namespace Domain\Services;

final class PopulatePublicationResult
{
    const SUCCESS = 0;
    const NOT_FOUND = 1;
    const ALREADY_POPULATED = 2;
    const PARSING_ERROR = 3;

    private $state;

    private $pmid;

    private $message;

    public static function success(int $pmid): self
    {
        return new self(self::SUCCESS, $pmid);
    }

    public static function notFound(int $pmid): self
    {
        return new self(self::NOT_FOUND, $pmid);
    }

    public static function alreadyPopulated(int $pmid): self
    {
        return new self(self::ALREADY_POPULATED, $pmid);
    }

    public static function parsingError(int $pmid, string $message): self
    {
        return new self(self::PARSING_ERROR, $pmid, $message);
    }

    private function __construct(int $state, int $pmid, string $message = '')
    {
        $this->state = $state;
        $this->pmid = $pmid;
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

        return $alternative($this->pmid, $this->message);
    }
}
