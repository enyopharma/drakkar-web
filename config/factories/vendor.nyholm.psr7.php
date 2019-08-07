<?php

declare(strict_types=1);

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

use Nyholm\Psr7\Factory\Psr17Factory;

return [
    Psr17Factory::class => function ($container) {
        return new Psr17Factory;
    },

    UriFactoryInterface::class => function ($container) {
        return $container->get(Psr17Factory::class);
    },

    StreamFactoryInterface::class => function ($container) {
        return $container->get(Psr17Factory::class);
    },

    ResponseFactoryInterface::class => function ($container) {
        return $container->get(Psr17Factory::class);
    },

    UploadedFileFactoryInterface::class => function ($container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function ($container) {
        return $container->get(Psr17Factory::class);
    },
];
