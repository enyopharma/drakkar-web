<?php declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodProjection;
use App\ReadModel\NotFoundException;
use App\ReadModel\RepositoryInterface;
use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
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
        $attributes = (array) $request->getAttributes();

        $methods = $this->repo->projection(MethodProjection::class);

        try {
            return $this->responder->response([
                'method' => $methods->rset($attributes)->first(),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
