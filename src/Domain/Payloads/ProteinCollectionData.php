<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class ProteinCollectionData extends DomainData
{
    public function __construct(array $proteins)
    {
        parent::__construct($proteins);
    }
}
