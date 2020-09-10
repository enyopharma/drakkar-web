<?php

declare(strict_types=1);

namespace App\Endpoints\Methods;

use App\ReadModel\MethodViewInterface;

final class IndexEndpoint
{
    private MethodViewInterface $methods;

    public function __construct(MethodViewInterface $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return array
     */
    public function __invoke(callable $input)
    {
        $query = $input('query', '');
        $limit = (int) $input('limit', 5);

        return $this->methods->search($query, $limit)->fetchAll();
    }
}
