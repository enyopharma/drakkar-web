<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\MethodCollectionData;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\MethodViewInterface;

final class SearchMethods
{
    private $methods;

    public function __construct(MethodViewInterface $methods)
    {
        $this->methods = $methods;
    }

    public function __invoke(string $q, int $limit): DomainPayloadInterface
    {
        $methods = $this->methods->search($q, $limit)->fetchAll();

        return new MethodCollectionData($methods);
    }
}
