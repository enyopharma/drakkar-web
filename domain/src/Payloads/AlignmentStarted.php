<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class AlignmentStarted implements DomainPayloadInterface
{
    public function data(): array
    {
        return [];
    }

    public function meta(): array
    {
        return [];
    }
}
