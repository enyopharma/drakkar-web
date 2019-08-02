<?php

declare(strict_types=1);

namespace Domain\Payloads;

interface DomainPayloadInterface
{
    public function data(): array;

    public function meta(): array;
}
