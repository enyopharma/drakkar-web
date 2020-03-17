<?php

declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunViewInterface;

use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $responder;

    private $runs;

    public function __construct(HtmlResponder $responder, RunViewInterface $runs)
    {
        $this->responder = $responder;
        $this->runs = $runs;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->success('runs/index', [
            'runs' => $this->runs->all()->fetchAll(),
        ]);
    }
}
