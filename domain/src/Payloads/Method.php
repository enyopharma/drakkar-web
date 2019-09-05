<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class Method extends DomainData
{
    public function __construct(array $method)
    {
        parent::__construct($method);
    }
}
