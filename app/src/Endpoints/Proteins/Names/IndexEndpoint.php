<?php

declare(strict_types=1);

namespace App\Endpoints\Proteins\Names;

use App\ReadModel\ProteinViewInterface;
use App\ReadModel\ProteinNameViewInterface;

final class IndexEndpoint
{
    public function __construct(
        private ProteinViewInterface $proteins,
        private ProteinNameViewInterface $names
    ) {
    }

    public function __invoke(callable $input): array|null
    {
        $id = (int) $input('id');

        if (!$protein = $this->proteins->id($id)->fetch()) {
            return null;
        }

        return $this->names->names($protein['id'])->fetchAll();
    }
}
