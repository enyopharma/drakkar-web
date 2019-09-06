<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\DomainData;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\MethodViewInterface;

final class SelectMethod implements DomainActionInterface
{
    private $methods;

    public function __construct(MethodViewInterface $methods)
    {
        $this->methods = $methods;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $psimi_id = (string) $input['psimi_id'];

        if ($method = $this->methods->psimiId($psimi_id)->fetch()) {
            return new DomainData($method);
        }

        return new ResourceNotFound('method', ['psimi_id' => $psimi_id]);
    }
}
