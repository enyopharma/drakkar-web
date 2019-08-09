<?php

declare(strict_types=1);

namespace App\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

final class NyholmContext
{
    private $app;

    public function __construct(callable $app)
    {
        $this->app = $app;
    }

    public function __invoke(...$xs): ResponseInterface
    {
        $factory = new Psr17Factory;

        $creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

        $request = $creator->fromGlobals();

        $handler = ($this->app)(...$xs);

        if (! $handler instanceof RequestHandlerInterface) {
            throw new \UnexpectedValueException(
                $this->unexpectedRequestHandlerTypeErrorMessage($handler)
            );
        }

        return $handler->handle($request);
    }

    private function unexpectedRequestHandlerTypeErrorMessage($handler): string
    {
        return vsprintf('%s expect the app callable to return an implementation of %s, %s returned', [
            self::class,
            RequestHandlerInterface::class,
            is_object($handler)
                ? 'instance of ' . get_class($handler)
                : gettype($handler),
        ]);
    }
}
