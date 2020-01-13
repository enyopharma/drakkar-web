<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\DeleteDescriptionResult;
use Domain\Actions\DeleteDescriptionInterface;

use App\Http\Responders\JsonResponder;

final class DeleteHandler implements RequestHandlerInterface
{
    private $responder;

    private $action;

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
            DeleteDescriptionResult::NOT_FOUND => function () use ($request) {
                return $this->responder->notFound($request);
            },
        ]);
    }
}
