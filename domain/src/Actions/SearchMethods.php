<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\DomainDataCollection;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\MethodViewInterface;

final class SearchMethods implements DomainActionInterface
{
    private $methods;

    public function __construct(MethodViewInterface $methods)
    {
        $this->methods = $methods;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $q = (string) ($input['q'] ?? '');
        $limit = (int) ($input['limit'] ?? 5);

        $methods = $this->methods->search($q, $limit)->fetchAll();

        return new DomainDataCollection($methods);
    }
}
