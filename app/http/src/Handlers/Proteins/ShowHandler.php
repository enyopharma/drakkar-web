<?php

declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\ProteinViewInterface;

use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $responder;

    private $proteins;

    public function __construct(JsonResponder $responder, ProteinViewInterface $proteins)
    {
        $this->responder = $responder;
        $this->proteins = $proteins;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $accession = (string) $request->getAttribute('accession');

        return ($protein = $this->proteins->accession($accession)->fetch())
            ? $this->responder->success($protein)
            : $this->responder->notFound($request);
    }
}
