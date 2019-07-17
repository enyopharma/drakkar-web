<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\NotFoundException;
use App\ReadModel\RepositoryInterface;
use App\ReadModel\PublicationProjection;
use App\ReadModel\DescriptionProjection;
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

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];

        $publications = $this->repo->projection(PublicationProjection::class, $run_id);
        $descriptions = $this->repo->projection(DescriptionProjection::class, $run_id, $pmid);

        try {
            return $this->responder->template('publications/show', [
                'publication' => $publications->rset($attributes)->first(),
                'descriptions' => $descriptions->rset($query),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }

        catch (\OutOfRangeException $e) {
            return $this->responder->route('runs.publications.show', ['run_id' => $run_id, 'pmid' => $pmid], [
                'page' => 1,
            ]);
        }
    }
}
