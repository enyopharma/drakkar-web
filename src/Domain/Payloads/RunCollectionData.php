<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class RunCollectionData extends DomainData
{
    public function __construct(array $runs)
    {
        parent::__construct($runs);
    }
}
