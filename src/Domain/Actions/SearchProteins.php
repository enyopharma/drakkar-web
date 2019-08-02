<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\ProteinCollectionData;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\ProteinViewInterface;

final class SearchProteins
{
    private $proteins;

    public function __construct(ProteinViewInterface $proteins)
    {
        $this->proteins = $proteins;
    }

    public function __invoke(string $type, string $q, int $limit): DomainPayloadInterface
    {
        $proteins = $this->proteins->search($type, $q, $limit)->fetchAll();

        return new ProteinCollectionData($proteins);
    }
}
