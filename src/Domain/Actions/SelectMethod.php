<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\MethodData;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\MethodViewInterface;

final class SelectMethod
{
    private $methods;

    public function __construct(MethodViewInterface $methods)
    {
        $this->methods = $methods;
    }

    public function __invoke(string $psimi_id): DomainPayloadInterface
    {
        if ($method = $this->methods->psimiId($psimi_id)->fetch()) {
            return new MethodData($method);
        }

        return new ResourceNotFound('method', ['psimi_id' => $psimi_id]);
    }
}
