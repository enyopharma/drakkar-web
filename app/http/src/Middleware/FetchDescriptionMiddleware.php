<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\ReadModel\DescriptionViewInterface;

final class FetchDescriptionMiddleware implements MiddlewareInterface
{
    private $factory;

    private $descriptions;

    public function __construct(ResponseFactoryInterface $factory, DescriptionViewInterface $descriptions)
    {
        $this->factory = $factory;
        $this->descriptions = $descriptions;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');
        $id = (int) $request->getAttribute('id');

        $select_description_sth = $this->descriptions->id($run_id, $pmid, $id);

        if (! $description = $select_description_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute('description', $description);

        return $handler->handle($request);
    }
}
