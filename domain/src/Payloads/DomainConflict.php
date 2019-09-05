<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class DomainConflict implements DomainPayloadInterface
{
    private $tpl;

    private $xs;

    public function __construct(string $tpl, ...$xs)
    {
        $this->tpl = $tpl;
        $this->xs = $xs;
    }

    public function reason(): string
    {
        return vsprintf($this->tpl, $this->xs);
    }

    public function data(): array
    {
        return [];
    }

    public function meta(): array
    {
        return [
            'reason' => $this->reason(),
        ];
    }
}
