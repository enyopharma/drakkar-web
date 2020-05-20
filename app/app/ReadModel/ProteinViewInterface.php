<?php

declare(strict_types=1);

namespace App\ReadModel;

interface ProteinViewInterface
{
    public function accession(string $accession, string ...$with): Statement;

    public function search(string $type, string $query, int $limit): Statement;
}
