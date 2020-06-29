<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ServerRequestInterface;

use League\Plates\Engine;

use App\Routing\UrlGenerator;
use App\ReadModel\RunInterface;
use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\ReadModel\DescriptionViewInterface;

final class IndexEndpoint
{
    private Engine $engine;

    private UrlGenerator $generator;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    private DescriptionViewInterface $descriptions;

    public function __construct(
        Engine $engine,
        UrlGenerator $generator,
        RunViewInterface $runs,
        AssociationViewInterface $associations,
        DescriptionViewInterface $descriptions
    ) {
        $this->engine = $engine;
        $this->generator = $generator;
        $this->runs = $runs;
        $this->associations = $associations;
        $this->descriptions = $descriptions;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|string|false
     */
    public function __invoke(ServerRequestInterface $request, callable $responder)
    {
        // parse request.
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $params = (array) $request->getQueryParams();

        $page = (int) ($params['page'] ?? 1);
        $limit = (int) ($params['limit'] ?? 20);

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return false;
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return false;
        }

        // get the descriptions.
        $total = $this->descriptions->count($run_id, $pmid);
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $responder(302, $this->outOfRangeUrl($publication, 1, 20));
        }

        if ($page < 1) {
            return $responder(302, $this->outOfRangeUrl($publication, 1, $limit));
        }

        if ($offset > 0 && $offset > $total) {
            return $responder(302, $this->outOfRangeUrl($publication, (int) ceil($total/$limit), $limit));
        }

        // success!
        return $this->engine->render('descriptions/index', [
            'run' => $run,
            'publication' => $publication,
            'descriptions' => $this->descriptions->all($run_id, $pmid, $limit, $offset)->fetchAll(),
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }

    private function outOfRangeUrl(array $publication, int $page, int $limit): string
    {
        $query = ['page' => $page, 'limit' => $limit];

        return $this->generator->generate('runs.publications.descriptions.index', $publication, $query, 'descriptions');
    }
}