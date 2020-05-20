<?php

declare(strict_types=1);

namespace App\Handlers\Dataset;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use GuzzleHttp\Psr7;

use App\ReadModel\Statement;
use App\ReadModel\DatasetViewInterface;

use App\Responders\FileResponder;

final class DownloadHandler implements RequestHandlerInterface
{
    private FileResponder $responder;

    private DatasetViewInterface $dataset;

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

    /**
     * @return \Generator<string>
     */
    private function generator(Statement $sth): \Generator
    {
        while ($description = $sth->fetch()) {
            yield json_encode($description->data()) . "\n";
        }
    }
}
