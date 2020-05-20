<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\ReadModel\RunInterface;
use App\ReadModel\RunViewInterface;

final class FetchRunMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $factory;

    private RunViewInterface $runs;

    public function __construct(ResponseFactoryInterface $factory, RunViewInterface $runs)
    {
        $this->factory = $factory;
        $this->runs = $runs;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');

        $select_run_sth = $this->runs->id($run_id);

        if (! $run = $select_run_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute(RunInterface::class, $run);

        return $handler->handle($request);
    }
}
