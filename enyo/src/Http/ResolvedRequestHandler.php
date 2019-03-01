<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Enyo\InstanceFactory;

final class ResolvedRequestHandler implements RequestHandlerInterface
{
    private $container;

    private $value;

    public function __construct(ContainerInterface $container, $value)
    {
        $this->container = $container;
        $this->value = $value;
    }

    public function handle(Request $request): Response
    {
        try {
            $handler = $this->resolved();
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                vsprintf('Unable to create a request handler from value %s', [
                    preg_replace('/\s+/', ' ', print_r($this->value, true)),
                ])
            );
        }

        return $handler->handle($request);
    }

    private function resolved(): RequestHandlerInterface
    {
        if (is_callable($this->value)) {
            return new CallableRequestHandler($this->value);
        }

        if (is_string($this->value)) {
            return $this->container->has($this->value)
                ? new RequestHandlerContainerEntry($this->container, $this->value)
                : new AutowiredRequestHandler(new InstanceFactory($this->container), $this->value);
        }

        return $this->value;
    }
}
