<?php declare(strict_types=1);

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

use Nyholm\Psr7\Factory\Psr17Factory;

return [
    'aliases' => [
        UriFactoryInterface::class => Psr17Factory::class,
        StreamFactoryInterface::class => Psr17Factory::class,
        ResponseFactoryInterface::class => Psr17Factory::class,
        UploadedFileFactoryInterface::class => Psr17Factory::class,
        ServerRequestFactoryInterface::class => Psr17Factory::class,
    ],

    'factories' => [
        Psr17Factory::class => function () {
            return new Psr17Factory;
        },
    ],
];
