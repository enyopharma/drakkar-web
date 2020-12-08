<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use Psr\Http\Message\ResponseInterface;

use League\Plates\Engine;

use App\Routing\UrlGenerator;
use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\Assertions\PublicationState;

final class IndexEndpoint
{
    public function __construct(
        private Engine $engine,
        private UrlGenerator $generator,
        private RunViewInterface $runs,
        private AssociationViewInterface $associations,
    ) {}

    public function __invoke(callable $input, callable $responder): ResponseInterface|string|false
    {
        // get input.
        $run_id = (int) $input('run_id');
        $state = $input('state', PublicationState::PENDING);
        $page = (int) $input('page', 1);
        $limit = (int) $input('limit', 20);

        // get the run.
        if (!$run = $this->runs->id($run_id, 'nbs')->fetch()) {
            return false;
        }

        // validate the publication state.
        if (!PublicationState::isValid($state)) {
            return false;
        }

        // get the publications.
        $total = $run['nbs'][$state];
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $responder(302, $this->outOfRangeUrl($run, $state, 1, 20));
        }

        if ($page < 1) {
            return $responder(302, $this->outOfRangeUrl($run, $state, 1, $limit));
        }

        if ($offset > 0 && $offset > $total) {
            return $responder(302, $this->outOfRangeUrl($run, $state, (int) ceil($total/$limit), $limit));
        }

        // success!
        return $this->engine->render('publications/index', [
            'run' => $run,
            'publications' => $this->associations->all($run_id, $state, $limit, $offset)->fetchAll(),
            'state' => $state,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
        ]);
    }

    private function outOfRangeUrl(array $run, string $state, int $page, int $limit): string
    {
        $query = ['state' => $state, 'page' => $page, 'limit' => $limit];

        return $this->generator->generate('runs.publications.index', $run, $query, 'publications');
    }
}
