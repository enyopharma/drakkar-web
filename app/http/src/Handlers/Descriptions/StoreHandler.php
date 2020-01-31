<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\StoreDescriptionResult;
use Domain\Actions\StoreDescriptionInterface;

use Domain\Input\DescriptionInput;

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
