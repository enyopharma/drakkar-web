<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

use App\Input\PeptideInput;

final class ValidatePeptideMiddleware implements MiddlewareInterface
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $input = PeptideInput::fromRequest($request);

            $request = $request->withAttribute(PeptideInput::class, $input);

            return $handler->handle($request);
        } catch (InvalidDataException $e) {
            return $this->failure(...$e->errors());
        }
    }

    private function failure(Error ...$errors): ResponseInterface
    {
        $contents = json_encode([
            'code' => 422,
            'success' => false,
            'errors' => array_map([$this, 'message'], $errors),
            'data' => [],
        ], JSON_THROW_ON_ERROR);

        $response = $this->factory
            ->createResponse(422)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write($contents);

        return $response;
    }

    private function message(Error $error): string
    {
        $name = array_map(fn ($key) => '[' . $key . ']', $error->keys());
        $name = implode('', $name);

        return $name == ''
            ? $error->message()
            : $name . ' => ' . $error->message();
    }
}
