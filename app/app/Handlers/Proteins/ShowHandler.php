<?php

declare(strict_types=1);

namespace App\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\ProteinViewInterface;
use App\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private JsonResponder $responder;

    private ProteinViewInterface $proteins;

    public function __construct(JsonResponder $responder, ProteinViewInterface $proteins)
    {
        $this->responder = $responder;
        $this->proteins = $proteins;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accession = $request->getAttribute('accession');

        $protein = $this->proteins
            ->accession($accession, 'isoforms', 'chains', 'domains', 'matures')
            ->fetch();

        return $protein
            ? $this->responder->success($protein)
            : $this->responder->notFound();
    }
}
