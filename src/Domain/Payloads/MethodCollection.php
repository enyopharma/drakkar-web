<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class MethodCollection extends DomainData
{
    public function __construct(array $methods)
    {
        parent::__construct($methods);
    }
}
