<?php

declare(strict_types=1);

use App\Responders\HtmlResponder;
use App\Responders\JsonResponder;
use App\Responders\FileResponder;

return [
    HtmlResponder::class => fn ($container) => new HtmlResponder(
        $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
        $container->get(League\Plates\Engine::class),
        $container->get(App\Routing\UrlGenerator::class),
    ),

    JsonResponder::class => fn ($container) => new JsonResponder(
        $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
    ),

    FileResponder::class => fn ($container) => new FileResponder(
        $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
    ),
];
