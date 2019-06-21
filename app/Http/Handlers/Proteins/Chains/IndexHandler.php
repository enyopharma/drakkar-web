<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins\Chains;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\Protein\ChainProjection;

use Enyo\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $chains;

    private $responder;

    public function __construct(ChainProjection $chains, JsonResponder $responder)
    {
        $this->chains = $chains;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'] ?? '';

        return $this->responder->response([
            'chains' => $this->chains->all($accession),
        ]);
    }
}
