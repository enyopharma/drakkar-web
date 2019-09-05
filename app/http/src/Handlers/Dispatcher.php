<?php

declare(strict_types=1);

namespace App\Http\Handlers;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Dispatcher implements RequestHandlerInterface
{
    private $handler;

    private $middleware;

    private $xs;

    public function __construct(
        RequestHandlerInterface $handler,
        MiddlewareInterface $middleware,
        MiddlewareInterface ...$xs
    ) {
        $this->handler = $handler;
        $this->middleware = $middleware;
        $this->xs = $xs;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = count($this->xs) > 0
            ? new self($this->handler, ...$this->xs)
            : $this->handler;

        return $this->middleware->process($request, $handler);
    }
}
