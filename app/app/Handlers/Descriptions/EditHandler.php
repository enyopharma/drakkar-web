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

final class EditHandler implements RequestHandlerInterface
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
        $description_id = (int) $request->getAttribute('description_id');

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return $this->responder->notFound();
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return $this->responder->notFound();
        }

        // get the description.
        if (!$description = $this->descriptions->id($run_id, $pmid, $description_id)->fetch()) {
            return $this->responder->notFound();
        }

        return $this->responder->success('descriptions/form', [
            'run' => $run,
            'publication' => $publication,
            'description' => $description,
        ]);
    }
}
