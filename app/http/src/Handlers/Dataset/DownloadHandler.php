<?php

declare(strict_types=1);

namespace App\Http\Handlers\Dataset;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use GuzzleHttp\Psr7;

use Domain\ReadModel\Statement;
use Domain\ReadModel\DatasetViewInterface;

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

        $filename = sprintf('vinland-%s-%s', $type, date('Y-m-d'));

        $stream = Psr7\stream_for($this->generator($dataset));

        return $this->responder->text($filename, $stream);
    }

    private function generator(Statement $sth): \Generator
    {
        while ($description = $sth->fetch()) {
            yield json_encode($description->data()) . "\n";
        }
    }
}
