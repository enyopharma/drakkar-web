<?php

declare(strict_types=1);

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

return [
    UriFactoryInterface::class => function () {
        return new Http\Factory\Guzzle\UriFactory;
    },

    StreamFactoryInterface::class => function () {
        return new Http\Factory\Guzzle\StreamFactory;
    },

    ResponseFactoryInterface::class => function () {
        return new Http\Factory\Guzzle\ResponseFactory;
    },
];
