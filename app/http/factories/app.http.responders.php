<?php

declare(strict_types=1);

use App\Http\Responders\RunResponder;
use App\Http\Responders\JsonResponder;
use App\Http\Responders\FormResponder;
use App\Http\Responders\DatasetResponder;
use App\Http\Responders\PublicationResponder;
use App\Http\Responders\DescriptionResponder;

return [
    RunResponder::class => function ($container) {
        return new RunResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            $container->get(League\Plates\Engine::class)
        );
    },

    PublicationResponder::class => function ($container) {
        return new PublicationResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            $container->get(League\Plates\Engine::class),
            $container->get(Zend\Expressive\Helper\UrlHelper::class)
        );
    },

    DescriptionResponder::class => function ($container) {
        return new DescriptionResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            $container->get(League\Plates\Engine::class),
            $container->get(Zend\Expressive\Helper\UrlHelper::class)
        );
    },

    FormResponder::class => function ($container) {
        return new FormResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class),
            $container->get(League\Plates\Engine::class)
        );
    },

    DatasetResponder::class => function ($container) {
        return new DatasetResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
        );
    },

    JsonResponder::class => function ($container) {
        return new JsonResponder(
            $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
        );
    },
];
