<?php

declare(strict_types=1);

use Nyholm\Psr7Server\ServerRequestCreator;

return [
    ServerRequestCreator::class => function ($container) {
        return new ServerRequestCreator(
            $container->get(Psr\Http\Message\UriFactoryInterface::class),
            $container->get(Psr\Http\Message\StreamFactoryInterface::class),
            $container->get(Psr\Http\Message\UploadedFileFactoryInterface::class),
            $container->get(Psr\Http\Message\ServerRequestFactoryInterface::class)
        );
    },
];
