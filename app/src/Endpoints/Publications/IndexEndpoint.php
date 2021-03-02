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

    public function __invoke(callable $input, callable $responder): ResponseInterface|string|null
    {
        // get input.
        $run_id = (int) $input('run_id');
        $state = $input('state', PublicationState::PENDING);
        $page = (int) $input('page', 1);
        $limit = (int) $input('limit', 20);

        // get the run.
        if (!$run = $this->runs->id($run_id, 'nbs')->fetch()) {
            return null;
        }

        // validate the publication state.
        if (!PublicationState::isValid($state)) {
            return null;
        }

        // get the publications.
        $total = $run['nbs'][$state];
        $offset = ($page - 1) * $limit;

        if ($limit < 0) {
            return $this->redirect($responder(), $run, $state, 1, 20);
        }

        if ($page < 1) {
            return $this->redirect($responder(), $run, $state, 1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return $this->redirect($responder(), $run, $state, (int) ceil($total/$limit), $limit);
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

    private function redirect(ResponseInterface $response, array $run, string $state, int $page, int $limit): ResponseInterface
    {
        $query = ['state' => $state, 'page' => $page, 'limit' => $limit];

        $url = $this->generator->generate('runs.publications.index', $run, $query, 'publications');

        return $response
            ->withStatus(302)
            ->withHeader('location', $url);
    }
}
