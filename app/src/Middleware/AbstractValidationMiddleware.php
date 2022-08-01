<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\Input\Validation\InvalidDataException;

abstract class AbstractValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private string $class, private ResponseFactoryInterface $factory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $factory = [$this->class, 'fromRequest'];

        if (!is_callable($factory)) {
            throw new \LogicException('The given class name must have a static function fromRequest');
        }

        try {
            $input = $factory($request);
        } catch (InvalidDataException $e) {
            return $this->failure($e);
        }

        $request = $request->withAttribute($this->class, $input);

        return $handler->handle($request);
    }

    private function failure(InvalidDataException $e): ResponseInterface
    {
        $contents = json_encode([
            'code' => 422,
            'success' => false,
            'errors' => $e->messages(),
            'data' => [],
        ], JSON_THROW_ON_ERROR);

        $response = $this->factory
            ->createResponse(422)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write($contents);

        return $response;
    }
}
