<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class RunCollection extends DomainData
{
    public function __construct(array $runs)
    {
        parent::__construct($runs);
    }
}
