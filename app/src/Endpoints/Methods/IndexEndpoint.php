<?php

declare(strict_types=1);

namespace App\Endpoints\Methods;

use Psr\Http\Message\ServerRequestInterface;

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
    public function __invoke(ServerRequestInterface $request)
    {
        $params = (array) $request->getQueryParams();

        $query = (string) ($params['query'] ?? '');
        $limit = (int) ($params['limit'] ?? 5);

        return $this->methods->search($query, $limit)->fetchAll();
    }
}
