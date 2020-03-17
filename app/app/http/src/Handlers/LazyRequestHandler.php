<?php declare(strict_types=1);

namespace App\Http\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LazyRequestHandler implements RequestHandlerInterface
{
    private $factory;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = ($this->factory)();

        if ($handler instanceof RequestHandlerInterface) {
            return $handler->handle($request);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s expects an instance of %s to be returned by the factory, %s returned', [
                self::class,
                RequestHandlerInterface::class,
                gettype($handler),
            ])
        );
    }
}
