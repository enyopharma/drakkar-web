<?php declare(strict_types=1);

namespace Enyo\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LazyRequestHandler implements RequestHandlerInterface
{
    private $factory;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function handle(Request $request): Response
    {
        $middleware = ($this->factory)();

        if ($middleware instanceof RequestHandlerInterface) {
            return $middleware->handle($request);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s expects an instance of %s to be returned by the factory, %s returned', [
                LazyRequestHandler::class,
                RequestHandlerInterface::class,
                gettype($middleware),
            ])
        );
    }
}
