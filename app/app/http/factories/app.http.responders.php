<?php

declare(strict_types=1);

use App\Http\Responders\HtmlResponder;
use App\Http\Responders\JsonResponder;
use App\Http\Responders\FileResponder;

return [
    HtmlResponder::class => function ($container) {
        return new HtmlResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            $container->get(League\Plates\Engine::class),
            $container->get(App\Http\UrlGenerator::class)
        );
    },

    JsonResponder::class => function ($container) {
        return new JsonResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
        );
    },

    FileResponder::class => function ($container) {
        return new FileResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
        );
    },
];
