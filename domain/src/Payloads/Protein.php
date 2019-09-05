<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class Protein extends DomainData
{
    public function __construct(array $protein)
    {
        parent::__construct($protein);
    }
}
