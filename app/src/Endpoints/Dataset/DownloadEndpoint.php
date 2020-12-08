<?php

declare(strict_types=1);

namespace App\Endpoints\Dataset;

use Psr\Http\Message\ResponseInterface;

use GuzzleHttp\Psr7;

use App\ReadModel\DatasetViewInterface;
use App\Assertions\RunType;

final class DownloadEndpoint
{
    public function __construct(
        private DatasetViewInterface $dataset,
    ) {}

    public function __invoke(callable $input, callable $responder): ResponseInterface|false
    {
        $type = $input('type');

        if (!RunType::isValid($type)) {
            return false;
        }

        $dataset = $this->dataset->all($type);

        $filename = sprintf('vinland-%s-%s', $type, date('Y-m-d'));

        $stream = Psr7\stream_for($this->generator($dataset));

        return $responder(200)
            ->withHeader('content-type', 'text/plain')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"')
            ->withBody($stream);
    }

    private function generator(iterable $descriptions): \Generator
    {
        foreach ($descriptions as $description) {
            yield json_encode($description) . "\n";
        }
    }
}
