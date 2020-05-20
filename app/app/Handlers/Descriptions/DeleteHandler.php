<?php

declare(strict_types=1);

namespace App\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Actions\DeleteDescriptionResult;
use App\Actions\DeleteDescriptionInterface;

use App\Responders\JsonResponder;

final class DeleteHandler implements RequestHandlerInterface
{
    private JsonResponder $responder;

    private DeleteDescriptionInterface $action;

    public function __construct(JsonResponder $responder, DeleteDescriptionInterface $action)
    {
        $this->responder = $responder;
        $this->action = $action;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        return $this->action->delete($id)->match([
            DeleteDescriptionResult::SUCCESS => function () {
                return $this->responder->success();
            },
            DeleteDescriptionResult::NOT_FOUND => function () {
                return $this->responder->notFound();
            },
        ]);
    }
}
