<?php

declare(strict_types=1);

namespace App\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Input\DescriptionInput;
use App\Actions\StoreDescriptionResult;
use App\Actions\StoreDescriptionInterface;
use App\Responders\JsonResponder;

final class StoreHandler implements RequestHandlerInterface
{
    private JsonResponder $responder;

    private StoreDescriptionInterface $action;

    public function __construct(JsonResponder $responder, StoreDescriptionInterface $action)
    {
        $this->responder = $responder;
        $this->action = $action;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get the description input.
        $input = $request->getAttribute(DescriptionInput::class);

        if (! $input instanceof DescriptionInput) {
            throw new \LogicException;
        }

        // store the description.
        return $this->action->store($input)->match([
            StoreDescriptionResult::SUCCESS => function ($description) {
                return $this->responder->success($description);
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
