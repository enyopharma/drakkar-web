<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\StoreDescriptionResult;
use Domain\Actions\StoreDescriptionInterface;

use App\Http\Responders\JsonResponder;

final class StoreHandler implements RequestHandlerInterface
{
    private $responder;

    private $action;

    public function __construct(JsonResponder $responder, StoreDescriptionInterface $action)
    {
        $this->responder = $responder;
        $this->action = $action;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $input = (array) $request->getParsedBody();

        return $this->action->store($run_id, $pmid, $input)->match([
            StoreDescriptionResult::SUCCESS => function ($description) {
                return $this->responder->success($description);
            },
            StoreDescriptionResult::INPUT_NOT_VALID => function ($_, ...$errors) {
                return $this->responder->errors(...$errors);
            },
            StoreDescriptionResult::ASSOCIATION_NOT_FOUND => function () {
                return $this->responder->notFound();
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