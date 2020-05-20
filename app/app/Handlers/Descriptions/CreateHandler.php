<?php

declare(strict_types=1);

namespace App\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunInterface;
use App\ReadModel\PublicationInterface;

use App\Responders\HtmlResponder;

final class CreateHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    public function __construct(HtmlResponder $responder)
    {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $run = $request->getAttribute(RunInterface::class);

        if (! $run instanceof RunInterface) {
            throw new \LogicException;
        }

        $publication = $request->getAttribute(PublicationInterface::class);

        if (! $publication instanceof PublicationInterface) {
            throw new \LogicException;
        }

        return $this->responder->success('descriptions/form', [
            'run' => $run->data(),
            'publication' => $publication->data(),
            'description' => [],
        ]);
    }
}