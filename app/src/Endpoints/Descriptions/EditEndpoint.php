<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use League\Plates\Engine;

use App\ReadModel\RunViewInterface;
use App\ReadModel\FormViewInterface;
use App\ReadModel\AssociationViewInterface;

final class EditEndpoint
{
    public function __construct(
        private Engine $engine,
        private RunViewInterface $runs,
        private AssociationViewInterface $associations,
        private FormViewInterface $descriptions,
    ) {}

    public function __invoke(callable $input): string|null
    {
        // parse request.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $id = (int) $input('id');
        $type = $input('type');

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

        // erase stable id when copying.
        if ($type == 'copy') {
            $description['stable_id'] = '';
        }

        // return the html.
        return $this->engine->render('descriptions/form', [
            'type' => $type,
            'run' => $run,
            'publication' => $publication,
            'description' => $description,
        ]);
    }
}
