<?php

declare(strict_types=1);

namespace App\Http\Handlers\Dataset;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\Drakkar;

use App\Responders\DatasetResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $drakkar;

    private $responder;

    public function __construct(Drakkar $drakkar, DatasetResponder $responder)
    {
        $this->drakkar = $drakkar;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $dataset = $this->drakkar->dataset();

            $filename = 'vinland-' . date('Y-m-d');

            return $this->responder->response($dataset, $filename);
        }

        catch (\Throwable $e) {
            return $this->responder->exception($request, $e);
        }
    }
}
