<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\ReadModel\PublicationViewInterface;

final class FetchPublicationMiddleware implements MiddlewareInterface
{
    private $factory;

    private $publications;

    public function __construct(ResponseFactoryInterface $factory, PublicationViewInterface $publications)
    {
        $this->factory = $factory;
        $this->publications = $publications;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $select_publication_sth = $this->publications->pmid($run_id, $pmid);

        if (! $publication = $select_publication_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute('publication', $publication);

        return $handler->handle($request);
    }
}
