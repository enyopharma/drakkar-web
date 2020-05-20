<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\ReadModel\RunInterface;
use App\ReadModel\PublicationInterface;
use App\ReadModel\DescriptionInterface;

final class FetchDescriptionMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $factory;

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

        $publication = $request->getAttribute(PublicationInterface::class);

        if (! $publication instanceof PublicationInterface) {
            throw new \LogicException;
        }

        $id = (int) $request->getAttribute('id');

        $select_description_sth = $publication->descriptions()->id($id);

        if (! $description = $select_description_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute(DescriptionInterface::class, $description);

        return $handler->handle($request);
    }
}
