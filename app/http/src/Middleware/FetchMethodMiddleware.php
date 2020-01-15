<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\ReadModel\MethodViewInterface;

final class FetchMethodMiddleware implements MiddlewareInterface
{
    private $factory;

    private $methods;

    public function __construct(ResponseFactoryInterface $factory, MethodViewInterface $methods)
    {
        $this->factory = $factory;
        $this->methods = $methods;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $psimi_id = (string) $request->getAttribute('psimi_id');

        $select_method_sth = $this->methods->psimiId($psimi_id);

        if (! $method = $select_method_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute('method', $method);

        return $handler->handle($request);
    }
}
