<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RequestHandlerContainerEntry implements RequestHandlerInterface
{
    private $container;

    private $id;

    public function __construct(ContainerInterface $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    public function handle(Request $request): Response
    {
        try {
            $handler = $this->container->get($this->id);
        }

        catch (\Throwable $e) {
            throw new \LogicException(
                sprintf('Unable to use container entry \'%s\' as a request handler', $this->id), 0, $e
            );
        }

        if ($handler instanceof RequestHandlerInterface) {
            return $handler->handle($request);
        }

        throw new \UnexpectedValueException(
            vsprintf('Container entry \'%s\' must implement %s to be used as a request handler, %s returned', [
                $this->id,
                RequestHandlerInterface::class,
                is_object($handler)
                    ? sprintf('instance of %s', get_class($handler))
                    : gettype($handler),
            ])
        );
    }
}
