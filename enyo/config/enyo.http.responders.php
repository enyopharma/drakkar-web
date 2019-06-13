<?php declare(strict_types=1);

use Enyo\Http\Responders\HtmlResponder;
use Enyo\Http\Responders\JsonResponder;

return [
    'factories' => [
        HtmlResponder::class => function ($container) {
            return new HtmlResponder(
                $container->get(Enyo\Http\Session::class),
                $container->get(Zend\Expressive\Helper\UrlHelper::class),
                $container->get(League\Plates\Engine::class),
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
            );
        },

        JsonResponder::class => function ($container) {
            return new JsonResponder(
                $container->get(Psr\Http\Message\ResponseFactoryInterface::class)
            );
        },
    ],
];
