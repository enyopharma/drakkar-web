<?php

declare(strict_types=1);

namespace App\ReadModel;

interface UniprotInterface
{
    public function protein(string $accession): Result;

    public function proteins(string $type, string $q, int $limit): ResultSet;
}
