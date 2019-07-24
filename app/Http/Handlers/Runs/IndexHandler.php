<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\DrakkarInterface;
use App\Http\Responders\HtmlResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $drakkar;

    private $responder;

    public function __construct(DrakkarInterface $drakkar, HtmlResponder $responder)
    {
        $this->drakkar = $drakkar;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->template('runs/index', [
            'user' => $request->getAttribute('user'),
            'runs' => $this->drakkar->runs(),
        ]);
    }
}
