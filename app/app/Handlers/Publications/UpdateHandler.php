<?php

declare(strict_types=1);

namespace App\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Actions\UpdatePublicationStateResult;
use App\Actions\UpdatePublicationStateInterface;

use App\Responders\HtmlResponder;

final class UpdateHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    private UpdatePublicationStateInterface $action;

    public function __construct(HtmlResponder $responder, UpdatePublicationStateInterface $action)
    {
        $this->responder = $responder;
        $this->action = $action;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $params = (array) $request->getParsedBody();

        $state = (string) ($params['state'] ?? '');
        $annotation = (string) ($params['annotation'] ?? '');
        $source = (string) ($params['_source'] ?? '');

        return $this->action->update($run_id, $pmid, $state, $annotation)->match([
            UpdatePublicationStateResult::SUCCESS => function () use ($source) {
                return $this->responder->temporary($source);
            },
            UpdatePublicationStateResult::NOT_FOUND => function () {
                return $this->responder->notFound();
            },
            UpdatePublicationStateResult::NOT_VALID => function () {
                return $this->responder->notFound();
            },
        ]);
    }
}
