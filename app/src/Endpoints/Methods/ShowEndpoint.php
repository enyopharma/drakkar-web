<?php

declare(strict_types=1);

namespace App\Endpoints\Methods;

use App\ReadModel\MethodViewInterface;

final class ShowEndpoint
{
    public function __construct(
        private MethodViewInterface $methods,
    ) {}

    public function __invoke(callable $input): array|null
    {
        $id = (int) $input('id');

        if ($method = $this->methods->id($id)->fetch()) {
            return $method;
        }

        return null;
    }
}
