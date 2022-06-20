<?php

declare(strict_types=1);

namespace App\Actions;

final class StoreDescriptionResult
{
    const SUCCESS = 0;
    const RUN_NOT_FOUND = 1;
    const ASSOCIATION_NOT_FOUND = 2;
    const INCONSISTENT_DATA = 3;
    const DESCRIPTION_ALREADY_EXISTS = 4;
    const FIRST_VERSION_FAILURE = 5;
    const NEW_VERSION_FAILURE = 6;

    public static function success(int $id): self
    {
        return new self(self::SUCCESS, $id);
    }

    public static function runNotFound(int $run_id): self
    {
        return new self(self::RUN_NOT_FOUND, null, sprintf('run not found (id => %s)', $run_id));
    }

    public static function associationNotFound(int $run_id, int $pmid): self
    {
        return new self(self::RUN_NOT_FOUND, null, sprintf('association not found (run_id => %s, pmid => %s)', $run_id, $pmid));
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
     * @param 0|1|2|3|4|5|6 $status
     */
    private function __construct(
        private int $status,
        private ?int $id = null,
        string ...$messages,
    ) {
        $this->messages = $messages;
    }

    /**
     * @return 0|1|2|3|4|5|6
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
