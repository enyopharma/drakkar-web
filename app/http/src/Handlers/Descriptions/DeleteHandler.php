<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Services\DeleteDescriptionResult;
use Domain\Services\DeleteDescriptionService;

use App\Http\Responders\JsonResponder;

final class DeleteHandler implements RequestHandlerInterface
{
    private $responder;

    private $service;

    public function __construct(JsonResponder $responder, DeleteDescriptionService $service)
    {
        $this->responder = $responder;
        $this->service = $service;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        return $this->service->delete($id)->match([
            DeleteDescriptionResult::SUCCESS => function () {
                return $this->responder->success();
            },
            DeleteDescriptionResult::NOT_FOUND => function () use ($request) {
                return $this->responder->notFound($request);
            },
        ]);
    }
}
