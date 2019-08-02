<?php

declare(strict_types=1);

namespace Domain\Payloads;

use Domain\ResourceInterface;

final class ResourceUpdated extends DomainUpdate
{
    public function __construct(ResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
