<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\UniprotInterface;
use App\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
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
        $query = (array) $request->getQueryParams();

        $type= $query['type'] ?? '';
        $q = $query['q'] ?? '';

        return $this->responder->response([
            'proteins' => $this->uniprot->proteins($type, $q, 5),
        ]);
    }
}
