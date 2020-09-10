<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use League\Plates\Engine;

use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;

final class CreateEndpoint
{
    private Engine $engine;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    public function __construct(
        Engine $engine,
        RunViewInterface $runs,
        AssociationViewInterface $associations
    ) {
        $this->engine = $engine;
        $this->runs = $runs;
        $this->associations = $associations;
    }

    /**
     * @return string|false
     */
    public function __invoke(callable $input)
    {
        // get input.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return false;
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return false;
        }

        return $this->engine->render('descriptions/form', [
            'type' => 'create',
            'run' => $run,
            'publication' => $publication,
            'description' => [],
        ]);
    }
}
