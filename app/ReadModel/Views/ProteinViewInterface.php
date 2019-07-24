<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

interface ProteinViewInterface
{
    /**
     * @return array|false
     */
    public function accession(string $accession);

    public function search(string $type, string $q, int $limit): array;
}
