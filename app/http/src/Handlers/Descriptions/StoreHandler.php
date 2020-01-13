<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Services\StoreDescriptionResult;
use Domain\Services\StoreDescriptionService;

use App\Http\Responders\JsonResponder;

final class StoreHandler implements RequestHandlerInterface
{
    private $responder;

    private $service;

    public function __construct(JsonResponder $responder, StoreDescriptionService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $input = (array) $request->getParsedBody();

        return $this->service->store($run_id, $pmid, $input)->match([
            StoreDescriptionResult::SUCCESS => function ($description) {
                return $this->responder->success($description);
            },
            StoreDescriptionResult::INPUT_NOT_VALID => function ($_, ...$errors) {
                return $this->responder->errors(...$errors);
            },
            StoreDescriptionResult::ASSOCIATION_NOT_FOUND => function () use ($request) {
                return $this->responder->notFound($request);
            },
            StoreDescriptionResult::DESCRIPTION_ALREADY_EXISTS => function () {
                return $this->responder->conflict('Description already exists');
            },
            StoreDescriptionResult::STABLE_ID_FAILURE => function () {
                return $this->responder->conflict('Failed to generate a stable id');
            },
        ]);
    }
}
