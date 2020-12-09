<?php

declare(strict_types=1);

namespace App\Actions;

final class DeleteDescriptionResult
{
    const SUCCESS = 0;
    const NOT_FOUND = 1;

    public static function success(): self
    {
        return new self(self::SUCCESS);
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    /**
     * @param 0|1 $status
     */
    private function __construct(
        private int $status,
    ) {}

    /**
     * @return 0|1
     */
    public function status()
    {
        return $this->status;
    }
}
