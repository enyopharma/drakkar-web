<?php

declare(strict_types=1);

namespace App\Routing;

use Psr\Http\Message\ServerRequestInterface;

use FastRoute\Dispatcher;

use Quanta\Http\RoutingResult;
use Quanta\Http\RouterInterface;

final class FastRouteRouter implements RouterInterface
{
    public function __construct(
        private Dispatcher $dispatcher,
    ) {}

    public function dispatch(ServerRequestInterface $request): RoutingResult
    {
        $info = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath(),
        );

        return match ($info[0]) {
            Dispatcher::NOT_FOUND => RoutingResult::notFound(),
            Dispatcher::METHOD_NOT_ALLOWED => RoutingResult::notAllowed(...$info[1]),
            default => RoutingResult::found($info[1], $info[2]),
        };
    }
}
