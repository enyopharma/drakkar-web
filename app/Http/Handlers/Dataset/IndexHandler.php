<?php declare(strict_types=1);

namespace App\Http\Handlers\Dataset;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\DatasetProjection;
use App\ReadModel\NotFoundException;
use App\ReadModel\RepositoryInterface;
use App\Http\Responders\DatasetResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $repo;

    private $responder;

    public function __construct(RepositoryInterface $repo, DatasetResponder $responder)
    {
        $this->repo = $repo;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dataset = $this->repo->projection(DatasetProjection::class);

        $rset = $dataset->rset();

        $filename = 'vinland-' . date('Y-m-d');

        return $this->responder->response($rset, $filename);
    }
}
