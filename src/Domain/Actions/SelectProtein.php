<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\ProteinData;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\ProteinViewInterface;

final class SelectProtein implements DomainActionInterface
{
    private $proteins;

    public function __construct(ProteinViewInterface $proteins)
    {
        $this->proteins = $proteins;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $accession = (string) $input['accession'];

        if ($protein = $this->proteins->accession($accession)->fetch()) {
            return new ProteinData($protein);
        }

        return new ResourceNotFound('protein', ['accession' => $accession]);
    }
}
