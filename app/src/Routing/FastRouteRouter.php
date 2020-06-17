<?php

declare(strict_types=1);

namespace App\Routing;

use Psr\Http\Message\ServerRequestInterface;

use FastRoute\Dispatcher;

use Quanta\Http\RoutingResult;
use Quanta\Http\RouterInterface;

final class FastRouteRouter implements RouterInterface
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(ServerRequestInterface $request): RoutingResult
    {
        $info = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath(),
        );

        if ($info[0] == Dispatcher::NOT_FOUND) {
            return RoutingResult::notFound();
        }

        if ($info[0] == Dispatcher::METHOD_NOT_ALLOWED) {
            return RoutingResult::notAllowed(...$info[1]);
        }

        return RoutingResult::found($info[1], $info[2]);
    }
}
