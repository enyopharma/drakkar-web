<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\NotFoundException;
use App\ReadModel\ProteinProjection;
use App\Http\Responders\JsonResponder;


final class ShowHandler implements RequestHandlerInterface
{
    private $proteins;

    private $responder;

    public function __construct(ProteinProjection $proteins, JsonResponder $responder)
    {
        $this->proteins = $proteins;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'] ?? '';

        try {
            return $this->responder->response([
                'protein' => $this->proteins->accession($accession),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
