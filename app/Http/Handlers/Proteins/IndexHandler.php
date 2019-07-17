<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\ProteinProjection;
use App\ReadModel\RepositoryInterface;
use App\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $repo;

    private $responder;

    public function __construct(RepositoryInterface $repo, JsonResponder $responder)
    {
        $this->repo = $repo;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $proteins = $this->repo->projection(ProteinProjection::class);

        return $this->responder->response([
            'proteins' => $proteins->rset($query),
        ]);
    }
}
