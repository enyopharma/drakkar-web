<?php

declare(strict_types=1);

namespace App\Endpoints\Methods;

use Psr\Http\Message\ServerRequestInterface;

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
    public function __invoke(ServerRequestInterface $request)
    {
        $psimi_id = $request->getAttribute('psimi_id');

        return $this->methods->psimiId($psimi_id)->fetch();
    }
}