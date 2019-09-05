<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Message\ResponseInterface;

final class Server
{
    private $application;

    public function __construct(callable $application)
    {
        $this->application = $application;
    }

    public function run()
    {
        $response = ($this->application)();

        $http_line = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        header($http_line, true, $response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        $stream = $response->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        while (! $stream->eof()) {
            echo $stream->read(1024 * 8);
        }
    }
}