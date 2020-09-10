<?php

declare(strict_types=1);

namespace App\Endpoints\Methods;

use App\ReadModel\MethodViewInterface;

final class ShowEndpoint
{
    private MethodViewInterface $methods;

    public function __construct(MethodViewInterface $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return array|false
     */
    public function __invoke(callable $input)
    {
        $id = (int) $input('id');

        return $this->methods->id($id)->fetch();
    }
}
