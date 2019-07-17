<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunProjection;
use App\ReadModel\RepositoryInterface;
use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $repo;

    private $responder;

    public function __construct(RepositoryInterface $repo, HtmlResponder $responder)
    {
        $this->repo = $repo;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $runs = $this->repo->projection(RunProjection::class);

        return $this->responder->template('runs/index', [
            'user' => $request->getAttribute('user'),
            'runs' => $runs->rset(),
        ]);
    }
}
