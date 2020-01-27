<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunInterface;
use Domain\ReadModel\PublicationInterface;
use Domain\ReadModel\DescriptionInterface;

use App\Http\Responders\HtmlResponder;

final class EditHandler implements RequestHandlerInterface
{
    private $responder;

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

        $description = $request->getAttribute(DescriptionInterface::class);

        if (! $description instanceof DescriptionInterface) {
            throw new \LogicException;
        }

        return $this->responder->success('descriptions/form', [
            'run' => $run->data(),
            'publication' => $publication->data(),
            'description' => $description->data(),
        ]);
    }
}
