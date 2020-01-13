<?php

declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Services\UpdatePublicationStateResult;
use Domain\Services\UpdatePublicationStateService;

use App\Http\Responders\HtmlResponder;

final class UpdateHandler implements RequestHandlerInterface
{
    private $responder;

    private $service;

    public function __construct(HtmlResponder $responder, UpdatePublicationStateService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $params = $request->getParsedBody();

        $state = (string) ($params['state'] ?? '');
        $annotation = (string) ($params['annotation'] ?? '');
        $url = (string) ($params['_source'] ?? '');

        return $this->service->update($run_id, $pmid, $state, $annotation)->match([
            UpdatePublicationStateResult::SUCCESS => function () use ($url) {
                return $this->responder->location($url);
            },
            UpdatePublicationStateResult::NOT_FOUND => function () use ($request) {
                return $this->responder->notFound($request);
            },
            UpdatePublicationStateResult::NOT_VALID => function () use ($request) {
                return $this->responder->notFound($request);
            },
        ]);
    }
}
