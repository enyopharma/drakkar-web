<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\RunProjection;

use Enyo\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $runs;

    private $responder;

    public function __construct(RunProjection $runs, HtmlResponder $responder)
    {
        $this->runs = $runs;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->template('runs/index', [
            'runs' => $this->runs->all(),
        ]);
    }
}
