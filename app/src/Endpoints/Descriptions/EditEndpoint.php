<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ServerRequestInterface;

use League\Plates\Engine;

use App\ReadModel\RunViewInterface;
use App\ReadModel\FormViewInterface;
use App\ReadModel\AssociationViewInterface;

final class EditEndpoint
{
    private Engine $engine;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    private FormViewInterface $descriptions;

    public function __construct(
        Engine $engine,
        RunViewInterface $runs,
        AssociationViewInterface $associations,
        FormViewInterface $descriptions
    ) {
        $this->engine = $engine;
        $this->runs = $runs;
        $this->associations = $associations;
        $this->descriptions = $descriptions;
    }

    /**
     * @return string|false
     */
    public function __invoke(ServerRequestInterface $request)
    {
        // parse request.
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');
        $id = (int) $request->getAttribute('id');
        $type = $request->getAttribute('type');

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return false;
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return false;
        }

        // get the description.
        if (!$description = $this->descriptions->id($run_id, $pmid, $id)->fetch()) {
            return false;
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
