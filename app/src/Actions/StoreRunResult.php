<?php

declare(strict_types=1);

namespace App\Actions;

final class StoreRunResult
{
    const SUCCESS = 0;
    const NO_PMID = 1;
    const RUN_ALREADY_EXISTS = 2;
    const ASSOCIATION_ALREADY_EXISTS = 3;

    private int $state;

    private ?int $run_id;

    private ?string $run_name;

    private ?int $pmid;

    public static function success(int $run_id): self
    {
        return new self(self::SUCCESS, $run_id);
    }

    public static function noPmid(): self
    {
        return new self(self::NO_PMID);
    }

    public static function runAlreadyExists(int $run_id, string $run_name): self
    {
        return new self(self::RUN_ALREADY_EXISTS, $run_id, $run_name);
    }

    public static function associationAlreadyExists(int $run_id, string $run_name, int $pmid): self
    {
        return new self(self::ASSOCIATION_ALREADY_EXISTS, $run_id, $run_name, $pmid);
    }

    private function __construct(int $state, int $run_id = null, string $run_name = null, int $pmid = null)
    {
        $this->state = $state;
        $this->run_id = $run_id;
        $this->run_name = $run_name;
        $this->pmid = $pmid;
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
            self::NO_PMID,
            self::RUN_ALREADY_EXISTS,
            self::ASSOCIATION_ALREADY_EXISTS,
        ];

        $keys = array_keys($alternatives);

        if (count(array_diff($all, $keys)) > 0) {
            throw new \InvalidArgumentException('missing alternatives');
        }

        if (!is_callable($alternative = $alternatives[$this->state])) {
            throw new \InvalidArgumentException('alternative must be a callable');
        }

        return $alternative($this->run_id, $this->run_name, $this->pmid);
    }
}
