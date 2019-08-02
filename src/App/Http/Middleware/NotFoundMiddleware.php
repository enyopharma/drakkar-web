<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Http\Responders\HtmlResponseFactory;

final class NotFoundMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(HtmlResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->factory->notfound(
            sprintf('Path %s does not exists', $request->getUri()->getPath())
        );
    }
}
