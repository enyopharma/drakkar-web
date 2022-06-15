<?php

declare(strict_types=1);

namespace App\Actions;

final class StorePeptideResult
{
    const SUCCESS = 0;
    const DESCRIPTION_NOT_FOUND = 1;
    const PEPTIDE_NOT_FOUND = 2;

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function descriptionNotFound(int $run_id, int $pmid, int $description_id): self
    {
        return new self(self::DESCRIPTION_NOT_FOUND, vsprintf('No description [%s, %s, %s]', [
            $run_id,
            $pmid,
            $description_id,
        ]));
    }

    public static function peptideNotFound(string $stable_id, string $type, string $sequence): self
    {
        return new self(self::PEPTIDE_NOT_FOUND, vsprintf('Description %s has no peptide [%s, %s]', [
            $stable_id,
            $type,
            $sequence,
        ]));
    }

    private array $messages;

    /**
     * @param 0|1|2 $status
     */
    private function __construct(private int $status, string ...$messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return 0|1|2
     */
    public function status()
    {
        return $this->status;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}
