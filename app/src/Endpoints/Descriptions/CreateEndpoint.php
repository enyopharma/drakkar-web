<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use League\Plates\Engine;

use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;

#[\App\Attributes\Pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create')]
#[\App\Attributes\Name('runs.publications.descriptions.create')]
final class CreateEndpoint
{
    public function __construct(
        private Engine $engine,
        private RunViewInterface $runs,
        private AssociationViewInterface $associations,
    ) {
    }

    public function __invoke(callable $input): string|null
    {
        // get input.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return null;
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return null;
        }

        return $this->engine->render('descriptions/form', [
            'type' => 'create',
            'run' => $run,
            'publication' => $publication,
            'description' => [],
        ]);
    }
}
