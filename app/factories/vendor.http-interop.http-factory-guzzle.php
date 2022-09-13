<?php

declare(strict_types=1);

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

return [
    UriFactoryInterface::class => Http\Factory\Guzzle\UriFactory::class,
    StreamFactoryInterface::class => Http\Factory\Guzzle\StreamFactory::class,
    ResponseFactoryInterface::class => Http\Factory\Guzzle\ResponseFactory::class,
];
