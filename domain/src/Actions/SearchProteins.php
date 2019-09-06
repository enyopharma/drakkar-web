<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\DomainDataCollection;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\ProteinViewInterface;

final class SearchProteins implements DomainActionInterface
{
    private $proteins;

    public function __construct(ProteinViewInterface $proteins)
    {
        $this->proteins = $proteins;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $type = (string) ($input['type'] ?? '');
        $q = (string) ($input['q'] ?? '');
        $limit = (int) ($input['limit'] ?? 5);

        $proteins = $this->proteins->search($type, $q, $limit)->fetchAll();

        return new DomainDataCollection($proteins);
    }
}
