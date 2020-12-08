<?php

declare(strict_types=1);

namespace App\Endpoints\Methods;

use App\ReadModel\MethodViewInterface;

final class IndexEndpoint
{
    public function __construct(
        private MethodViewInterface $methods,
    ) {}

    public function __invoke(callable $input): array
    {
        $query = $input('query', '');
        $limit = (int) $input('limit', 5);

        return $this->methods->search($query, $limit)->fetchAll();
    }
}
