<?php

declare(strict_types=1);

namespace App\Actions;

final class StoreDescriptionResult
{
    const SUCCESS = 0;
    const INCONSISTENT_DATA = 1;
    const DESCRIPTION_ALREADY_EXISTS = 2;
    const FIRST_VERSION_FAILURE = 3;
    const NEW_VERSION_FAILURE = 4;

    public static function success(int $id): self
    {
        return new self(self::SUCCESS, $id);
    }

    public static function inconsistentData(string $message, string ...$messages): self
    {
        return new self(self::INCONSISTENT_DATA, null, $message, ...$messages);
    }

    public static function descriptionAlreadyExists(): self
    {
        return new self(self::DESCRIPTION_ALREADY_EXISTS);
    }

    public static function firstVersionFailure(): self
    {
        return new self(self::FIRST_VERSION_FAILURE);
    }

    public static function newVersionFailure(): self
    {
        return new self(self::NEW_VERSION_FAILURE);
    }

    private array $messages;

    /**
     * @param 0|1|2|3|4 $status
     */
    private function __construct(
        private int $status,
        private ?int $id = null,
        string ...$messages,
    ) {
        $this->messages = $messages;
    }

    /**
     * @return 0|1|2|3|4
     */
    public function status()
    {
        return $this->status;
    }

    public function id(): int
    {
        $types = [self::SUCCESS];

        if (in_array($this->status, $types, true) && !is_null($this->id)) {
            return $this->id;
        }

        throw new \LogicException('Result has no id');
    }

    public function messages(): array
    {
        $types = [self::INCONSISTENT_DATA];

        if (in_array($this->status, $types, true)) {
            return $this->messages;
        }

        throw new \LogicException('Result has no messages');
    }
}
