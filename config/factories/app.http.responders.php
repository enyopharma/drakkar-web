<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use App\Http\Responders\RunResponder;
use App\Http\Responders\JsonResponder;
use App\Http\Responders\DatasetResponder;
use App\Http\Responders\PublicationResponder;
use App\Http\Responders\DescriptionResponder;

return [
    RunResponder::class => function ($container) {
        return new RunResponder(
            $container->get(ResponseFactoryInterface::class),
            $container->get(Engine::class)
        );
    },

    PublicationResponder::class => function ($container) {
        return new PublicationResponder(
            $container->get(ResponseFactoryInterface::class),
            $container->get(Engine::class),
            $container->get(UrlHelper::class)
        );
    },

    DescriptionResponder::class => function ($container) {
        return new DescriptionResponder(
            $container->get(ResponseFactoryInterface::class),
            $container->get(Engine::class),
            $container->get(UrlHelper::class)
        );
    },

    DatasetResponder::class => function ($container) {
        return new DatasetResponder(
            $container->get(ResponseFactoryInterface::class)
        );
    },

    JsonResponder::class => function ($container) {
        return new JsonResponder(
            $container->get(ResponseFactoryInterface::class)
        );
    },
];
