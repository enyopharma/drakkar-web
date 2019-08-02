<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface ProteinViewInterface
{
    public function accession(string $accession): Statement;

    public function search(string $type, string $q, int $limit): Statement;
}
