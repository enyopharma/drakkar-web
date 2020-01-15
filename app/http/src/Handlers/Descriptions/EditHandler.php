<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $run = $request->getAttribute('run');
        $publication = $request->getAttribute('publication');
        $description = $request->getAttribute('description');

        if (is_null($run) || is_null($publication) || is_null($description)) throw new \LogicException;

        return $this->responder->success('descriptions/form', [
            'run' => $run,
            'publication' => $publication,
            'description' => $description,
        ]);
    }
}
