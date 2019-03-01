<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Enyo\InstanceFactory;

final class AutowiredMiddleware implements MiddlewareInterface
{
    private $factory;

    private $class;

    public function __construct(InstanceFactory $factory, string $class)
    {
        $this->factory = $factory;
        $this->class = $class;
    }

    public function process(Request $request, Handler $handler): Response
    {
        try {
            $middleware = ($this->factory)($this->class);
        }

        catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to instantiate a middleware from class %s', $this->class), 0, $e
            );
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $handler);
        }

        throw new \UnexpectedValueException(
            vsprintf('Class %s must implement %s to be used as a middleware', [
                $this->class,
                MiddlewareInterface::class,
            ])
        );
    }
}
