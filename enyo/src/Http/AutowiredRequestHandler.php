<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Enyo\InstanceFactory;

final class AutowiredRequestHandler implements RequestHandlerInterface
{
    private $factory;

    private $class;

    public function __construct(InstanceFactory $factory, string $class)
    {
        $this->factory = $factory;
        $this->class = $class;
    }

    public function handle(Request $request): Response
    {
        try {
            $handler = ($this->factory)($this->class);
        }

        catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to instantiate a request handler from class %s', $this->class), 0, $e
            );
        }

        if ($handler instanceof RequestHandlerInterface) {
            return $handler->handle($request);
        }

        throw new \UnexpectedValueException(
            vsprintf('Class %s must implement %s to be used as a request handler', [
                $this->class,
                RequestHandlerInterface::class,
            ])
        );
    }
}
