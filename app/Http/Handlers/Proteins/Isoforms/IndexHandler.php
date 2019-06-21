<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins\Isoforms;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\Protein\IsoformProjection;

use Enyo\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $isoforms;

    private $responder;

    public function __construct(IsoformProjection $isoforms, JsonResponder $responder)
    {
        $this->isoforms = $isoforms;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'] ?? '';

        return $this->responder->response([
            'isoforms' => $this->isoforms->all($accession),
        ]);
    }
}
