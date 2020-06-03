<?php

declare(strict_types=1);

namespace App\Endpoints\Dataset;

use Psr\Http\Message\ServerRequestInterface;

use GuzzleHttp\Psr7;

use App\ReadModel\DatasetViewInterface;

final class DownloadEndpoint
{
    private DatasetViewInterface $dataset;

    public function __construct(DatasetViewInterface $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $responder)
    {
        $type = $request->getAttribute('type');

        $dataset = $this->dataset->all($type);

        $filename = sprintf('vinland-%s-%s', $type, date('Y-m-d'));

        $stream = Psr7\stream_for($this->generator($dataset));

        return $responder(200)
            ->withHeader('content-type', 'text/plain')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"')
            ->withBody($stream);
    }

    /**
     * @return \Generator<string>
     */
    private function generator(iterable $descriptions): \Generator
    {
        foreach ($descriptions as $description) {
            yield json_encode($description) . "\n";
        }
    }
}
