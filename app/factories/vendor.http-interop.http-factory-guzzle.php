<?php

declare(strict_types=1);

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

return [
    UriFactoryInterface::class => fn () => new Http\Factory\Guzzle\UriFactory,
    StreamFactoryInterface::class => fn () => new Http\Factory\Guzzle\StreamFactory,
    ResponseFactoryInterface::class => fn () => new Http\Factory\Guzzle\ResponseFactory,
];
