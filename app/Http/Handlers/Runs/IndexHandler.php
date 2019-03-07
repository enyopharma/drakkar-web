<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use App\Repositories\RunRepository;

final class IndexHandler implements RequestHandlerInterface
{
    private $runs;

    private $responder;

    public function __construct(
        RunRepository $runs,
        Responder $responder
    ) {
        $this->runs = $runs;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->html('runs/index', [
            'runs' => $this->runs->all(),
        ]);
    }
}
