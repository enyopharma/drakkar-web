<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;
use Domain\ReadModel\DescriptionViewInterface;

use App\Http\Responders\HtmlResponder;

final class EditHandler implements RequestHandlerInterface
{
    private $responder;

    private $runs;

    private $publications;

    private $descriptions;

    public function __construct(
        HtmlResponder $responder,
        RunViewInterface $runs,
        PublicationViewInterface $publications,
        DescriptionViewInterface $descriptions
    ) {
        $this->responder = $responder;
        $this->runs = $runs;
        $this->publications = $publications;
        $this->descriptions = $descriptions;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');
        $id = (int) $request->getAttribute('id');

        $select_run_sth = $this->runs->id($run_id);

        if (! $run = $select_run_sth->fetch()) {
            return $this->responder->notFound($request);
        }

        $select_publication_sth = $this->publications->pmid($run_id, $pmid);

        if (! $publication = $select_publication_sth->fetch()) {
            return $this->responder->notFound($request);
        }

        $select_description_sth = $this->descriptions->id($run_id, $pmid, $id);

        if (! $description = $select_description_sth->fetch()) {
            return $this->responder->notFound($request);
        }

        return $this->responder->success('descriptions/form', [
            'run' => $run,
            'publication' => $publication,
            'description' => $description,
        ]);
    }
}
