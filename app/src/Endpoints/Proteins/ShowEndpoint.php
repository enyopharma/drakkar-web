<?php

declare(strict_types=1);

namespace App\Endpoints\Proteins;

use App\ReadModel\ProteinViewInterface;

final class ShowEndpoint
{
    private ProteinViewInterface $proteins;

    public function __construct(ProteinViewInterface $proteins)
    {
        $this->proteins = $proteins;
    }

    /**
     * @return array|false
     */
    public function __invoke(callable $input)
    {
        $id = (int) $input('id');

        return $this->proteins
            ->id($id, 'isoforms', 'chains', 'domains', 'matures')
            ->fetch();
    }
}
