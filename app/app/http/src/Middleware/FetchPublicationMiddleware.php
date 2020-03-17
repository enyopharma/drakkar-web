<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\ReadModel\RunInterface;
use Domain\ReadModel\PublicationInterface;

final class FetchPublicationMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $run = $request->getAttribute(RunInterface::class);

        if (! $run instanceof RunInterface) {
            throw new \LogicException;
        }

        $pmid = (int) $request->getAttribute('pmid');

        $select_publication_sth = $run->publications()->pmid($pmid);

        if (! $publication = $select_publication_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute(PublicationInterface::class, $publication);

        return $handler->handle($request);
    }
}
