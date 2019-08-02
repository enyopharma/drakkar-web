<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class MethodData extends DomainData
{
    public function __construct(array $method)
    {
        parent::__construct($method);
    }
}
