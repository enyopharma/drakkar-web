<?php

declare(strict_types=1);

namespace App\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunInterface;
use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\ReadModel\DescriptionViewInterface;
use App\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    private DescriptionViewInterface $descriptions;

    public function __construct(
        HtmlResponder $responder,
        RunViewInterface $runs,
        AssociationViewInterface $associations,
        DescriptionViewInterface $descriptions
    ) {
        $this->responder = $responder;
        $this->runs = $runs;
        $this->associations = $associations;
        $this->descriptions = $descriptions;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // parse request.
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $params = (array) $request->getQueryParams();

        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return $this->responder->notFound();
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return $this->responder->notFound();
        }

        // get the descriptions.
        $total = $this->descriptions->count($run_id, $pmid);
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $this->outOfRangeResponse($publication, 1, 20);
        }

        if ($page < 1) {
            return $this->outOfRangeResponse($publication, 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->outOfRangeResponse($publication, (int) ceil($total/$limit), $limit);
        }

        // success!
        return $this->responder->success('descriptions/index', [
            'run' => $run,
            'publication' => $publication,
            'descriptions' => $this->descriptions->all($run_id, $pmid, $limit, $offset)->fetchAll(),
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    private function outOfRangeResponse(array $publication, int $page, int $limit): ResponseInterface
    {
        $query = ['page' => $page, 'limit' => $limit];

        return $this->responder->temporary('runs.publications.descriptions.index', $publication, $query, 'descriptions');
    }
}
