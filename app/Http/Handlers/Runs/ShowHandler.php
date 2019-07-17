<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Publication;
use App\ReadModel\RunProjection;
use App\ReadModel\NotFoundException;
use App\ReadModel\RepositoryInterface;
use App\ReadModel\PublicationProjection;
use App\Http\Responders\HtmlResponder;

final class ShowHandler implements RequestHandlerInterface
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
        $attributes = (array) $request->getAttributes();
        $query = (array) $request->getQueryParams();

        $id = (int) $attributes['id'];

        $state = $query['state'] ?? \App\Domain\Publication::PENDING;

        if (! in_array($state, Publication::STATES)) {
            return $this->responder->notfound();
        }

        $runs = $this->repo->projection(RunProjection::class);
        $publications = $this->repo->projection(PublicationProjection::class, $id, $state);

        try {
            return $this->responder->template('runs/show', [
                'state' => $state,
                'run' => $runs->rset($attributes)->first(),
                'publications' => $publications->rset($query),
            ]);
        }

        catch (NotFoundException $e) {
            throw $e;
            return $this->responder->notfound();
        }

        catch (\OutOfRangeException $e) {
            return $this->responder->route('runs.show', ['id' => $id], [
                'state' => $state,
                'page' => 1,
            ]);
        }
    }
}
