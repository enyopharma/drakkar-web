<?php

declare(strict_types=1);

namespace App\Endpoints\Proteins;

use App\ReadModel\ProteinViewInterface;
use App\Assertions\ProteinType;

final class IndexEndpoint
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
        $type = $input('type', '');
        $query = $input('query', '');
        $limit = (int) $input('limit', 5);

        if (!ProteinType::isValid($type)) {
            return false;
        }

        return $this->proteins->search($type, $query, $limit)->fetchAll();
    }
}
