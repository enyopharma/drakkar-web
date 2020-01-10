<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;
use Domain\ReadModel\DescriptionViewInterface;

use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $responder;

    private $runs;

    private $publications;

    private $descriptions;

    public function __construct(
        HtmlResponder $responder,
        RunViewInterface $runs,
        PublicationViewInterface $publications,
        DescriptionViewInterface $descriptions
    ) {
        $this->responder = $responder;
        $this->runs = $runs;
        $this->publications = $publications;
        $this->descriptions = $descriptions;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get input.
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $params = (array) $request->getQueryParams();

        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the run.
        $select_run_sth = $this->runs->id($run_id);

        if (! $run = $select_run_sth->fetch()) {
            return $this->responder->notFound($request);
        }

        // get the publication.
        $select_publication_sth = $this->publications->pmid($run_id, $pmid);

        if (! $publication = $select_publication_sth->fetch()) {
            return $this->responder->notFound($request);
        }

        // get the descriptions.
        $offset = ($page - 1) * $limit;
        $total = $this->descriptions->count($run_id, $pmid);

        if ($limit < 0) {
            return $this->outOfRangeResponse($run_id, $pmid, 1, 20);
        }

        if ($page < 1) {
            return $this->outOfRangeResponse($run_id, $pmid, 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->outOfRangeResponse($run_id, $pmid, (int) ceil($total/$limit), $limit);
        }

        // get the descriptions.
        $descriptions = $this->descriptions->all($run_id, $pmid, $limit, $offset)->fetchAll();

        // success!
        return $this->responder->success('descriptions/index', [
            'run' => $run,
            'publication' => $publication,
            'descriptions' => $descriptions,
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    private function outOfRangeResponse(int $run_id, int $pmid, int $page, int $limit): ResponseInterface
    {
        $params = ['run_id' => $run_id, 'pmid' => $pmid];

        $query = ['page' => $page, 'limit' => $limit];

        return $this->responder->route('runs.publications.descriptions.index', $params, $query, 'descriptions');
    }
}
