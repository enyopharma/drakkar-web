<?php

declare(strict_types=1);

namespace App\Endpoints\Proteins;

use App\ReadModel\ProteinViewInterface;

final class ShowEndpoint
{
    public function __construct(
        private ProteinViewInterface $proteins,
    ) {}

    public function __invoke(callable $input): array|null
    {
        $id = (int) $input('id');

        $with = ['isoforms', 'chains', 'domains', 'matures'];

        if ($protein = $this->proteins->id($id, ...$with)->fetch()) {
            return $protein;
        }

        return null;
    }
}
