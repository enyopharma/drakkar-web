<?php

declare(strict_types=1);

namespace App\Endpoints\Peptides;

final class IndexEndpoint
{
    public function __construct(
        private \League\Plates\Engine $engine,
        private \App\ReadModel\RunViewInterface $runs,
        private \App\ReadModel\AssociationViewInterface $associations,
        private \App\ReadModel\FormViewInterface $descriptions,
        private \App\ReadModel\PeptideViewInterface $peptides,
    ) {
    }

    public function __invoke(callable $input): string|null
    {
        // parse request.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $id = (int) $input('id');

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return null;
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return null;
        }

        // get the description.
        if (!$description = $this->descriptions->id($run_id, $pmid, $id)->fetch()) {
            return null;
        }

        // get the peptides.
        $peptides = $this->peptides->all($run_id, $pmid, $id)->fetchAll();

        return $this->engine->render('peptides/index', [
            'type' => 'create',
            'run' => $run,
            'publication' => $publication,
            'description' => $description,
            'peptides' => $peptides,
        ]);
    }
}
