<?php

declare(strict_types=1);

namespace App\Http\Handlers\Dataset;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\DatasetViewInterface;

use App\Http\Streams\IteratorStream;
use App\Http\Responders\FileResponder;

final class DownloadHandler implements RequestHandlerInterface
{
    private $responder;

    private $dataset;

    public function __construct(FileResponder $responder, DatasetViewInterface $dataset)
    {
        $this->responder = $responder;
        $this->dataset = $dataset;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $type = $request->getAttribute('type');

        $dataset = $this->dataset->all($type);

        $stream = IteratorStream::json($dataset);
        $filename = sprintf('vinland-%s-%s', $type, date('Y-m-d'));

        return $this->responder->text($stream, $filename);
    }
}
