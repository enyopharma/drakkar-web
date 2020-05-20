<?php

declare(strict_types=1);

namespace App\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunInterface;
use App\ReadModel\RunViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\Responders\HtmlResponder;

final class CreateHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    private RunViewInterface $runs;

    private AssociationViewInterface $associations;

    public function __construct(
        HtmlResponder $responder,
        RunViewInterface $runs,
        AssociationViewInterface $associations
    ) {
        $this->responder = $responder;
        $this->runs = $runs;
        $this->associations = $associations;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // parse request.
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        // get the run.
        if (!$run = $this->runs->id($run_id)->fetch()) {
            return $this->responder->notFound();
        }

        // get the publication.
        if (!$publication = $this->associations->pmid($run_id, $pmid)->fetch()) {
            return $this->responder->notFound();
        }

        return $this->responder->success('descriptions/form', [
            'run' => $run,
            'publication' => $publication,
            'description' => [],
        ]);
    }
}
