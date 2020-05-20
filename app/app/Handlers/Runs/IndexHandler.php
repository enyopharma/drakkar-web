<?php

declare(strict_types=1);

namespace App\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunViewInterface;

use App\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    private RunViewInterface $runs;

    public function __construct(HtmlResponder $responder, RunViewInterface $runs)
    {
        $this->responder = $responder;
        $this->runs = $runs;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->success('runs/index', [
            'runs' => $this->runs->all('nbs')->fetchAll(),
        ]);
    }
}
