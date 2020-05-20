<?php

declare(strict_types=1);

namespace App\Responders;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class FileResponder
{
    private ResponseFactoryInterface $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function text(string $filename, StreamInterface $stream): ResponseInterface
    {
        return $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/plain')
            ->withHeader('content-disposition', 'attachment; filename="' . $filename . '"')
            ->withBody($stream);
    }
}
