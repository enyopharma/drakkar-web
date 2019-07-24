<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\UniprotInterface;
use App\ReadModel\NotFoundException;
use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $uniprot;

    private $responder;

    public function __construct(UniprotInterface $uniprot, JsonResponder $responder)
    {
        $this->uniprot = $uniprot;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'];

        try {
            return $this->responder->response([
                'protein' => $this->uniprot->protein($accession)->data(),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
