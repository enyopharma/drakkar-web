<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\DomainPayloadInterface;

interface DomainActionInterface
{
    public function __invoke(array $input): DomainPayloadInterface;
}
