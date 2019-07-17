<?php declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodProjection;
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

        $methods = $this->repo->projection(MethodProjection::class);

        return $this->responder->response([
            'methods' => $methods->rset($query),
        ]);
    }
}
