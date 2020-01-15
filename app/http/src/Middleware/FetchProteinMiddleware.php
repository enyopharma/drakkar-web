<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Domain\ReadModel\ProteinViewInterface;

final class FetchProteinMiddleware implements MiddlewareInterface
{
    private $factory;

    private $proteins;

    public function __construct(ResponseFactoryInterface $factory, ProteinViewInterface $proteins)
    {
        $this->factory = $factory;
        $this->proteins = $proteins;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $accession = (string) $request->getAttribute('accession');

        $select_protein_sth = $this->proteins->accession($accession);

        if (! $protein = $select_protein_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $request = $request->withAttribute('protein', $protein);

        return $handler->handle($request);
    }
}
