<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\ReadModel\MethodInterface;
use App\ReadModel\MethodViewInterface;

final class FetchMethodMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $factory;

    private MethodViewInterface $methods;

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

        $request = $request->withAttribute(MethodInterface::class, $method);

        return $handler->handle($request);
    }
}
