<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins\Matures;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\Protein\MatureProjection;

use Enyo\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $matures;

    private $responder;

    public function __construct(MatureProjection $matures, JsonResponder $responder)
    {
        $this->matures = $matures;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'] ?? '';

        return $this->responder->response([
            'matures' => $this->matures->all($accession),
        ]);
    }
}
