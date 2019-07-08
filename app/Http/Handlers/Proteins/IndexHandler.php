<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\ProteinProjection;
use App\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
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
        $query = (array) $request->getQueryParams();

        $type = $query['type'] ?? '';
        $q = $query['q'] ?? '';

        return $this->responder->response([
            'proteins' => $this->proteins->search($type, $q),
        ]);
    }
}
