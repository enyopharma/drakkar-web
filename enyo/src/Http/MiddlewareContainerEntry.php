<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MiddlewareContainerEntry implements MiddlewareInterface
{
    private $container;

    private $id;

    public function __construct(ContainerInterface $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    public function process(Request $request, Handler $handler): Response
    {
        try {
            $middleware = $this->container->get($this->id);
        }

        catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to use container entry \'%s\' as a middleware', $this->id), 0, $e
            );
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $handler);
        }

        throw new \UnexpectedValueException(
            vsprintf('Container entry \'%s\' must implement %s to be used as a middleware, %s returned', [
                $this->id,
                MiddlewareInterface::class,
                is_object($middleware)
                    ? sprintf('instance of %s', get_class($middleware))
                    : gettype($middleware),
            ])
        );
    }
}
