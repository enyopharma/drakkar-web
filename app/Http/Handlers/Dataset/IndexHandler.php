<?php declare(strict_types=1);

namespace App\Http\Handlers\Dataset;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\DrakkarInterface;
use App\Http\Responders\DatasetResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $drakkar;

    private $responder;

    public function __construct(DrakkarInterface $drakkar, DatasetResponder $responder)
    {
        $this->drakkar = $drakkar;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dataset = $this->drakkar->dataset();

        $filename = 'vinland-' . date('Y-m-d');

        return $this->responder->response($dataset, $filename);
    }
}
