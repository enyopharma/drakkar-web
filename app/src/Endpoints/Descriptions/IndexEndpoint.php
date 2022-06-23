<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ResponseInterface;

use League\Plates\Engine;

use Quanta\Http\UrlGenerator;

use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\ReadModel\DescriptionViewInterface;

#[\App\Attributes\Pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')]
#[\App\Attributes\Name('runs.publications.descriptions.index')]
final class IndexEndpoint
{
    public function __construct(
        private Engine $engine,
        private UrlGenerator $generator,
        private RunViewInterface $runs,
        private AssociationViewInterface $associations,
        private DescriptionViewInterface $descriptions,
    ) {
    }

    public function __invoke(callable $input, callable $responder): ResponseInterface|string|null
    {
        // get input.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $stable_id = $input('stable_id', '');
        $page = (int) $input('page', 1);
        $limit = (int) $input('limit', 20);

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return null;
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return null;
        }

        // get the descriptions.
        $total = $this->descriptions->count($run_id, $pmid, $stable_id);
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $this->redirect($responder(), $publication, 1, 20);
        }

        if ($page < 1) {
            return $this->redirect($responder(), $publication, 1, $limit);
        }

        if ($offset > 0 && $offset >= $total) {
            return $this->redirect($responder(), $publication, (int) ceil($total / $limit), $limit);
        }

        $descriptions = $this->descriptions->all($run_id, $pmid, $stable_id, $limit, $offset)->fetchAll();

        // success!
        return $this->engine->render('descriptions/index', [
            'run' => $run,
            'publication' => $publication,
            'descriptions' => $descriptions,
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
            'stable_id' => $stable_id,
        ]);
    }

    private function redirect(ResponseInterface $response, array $publication, int $page, int $limit): ResponseInterface
    {
        $query = ['page' => $page, 'limit' => $limit];

        $url = $this->generator->generate('runs.publications.descriptions.index', $publication, $query, 'descriptions');

        return $response
            ->withStatus(302)
            ->withHeader('location', $url);
    }
}
