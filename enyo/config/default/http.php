<?php declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use Enyo\Http\Session;
use Enyo\Http\Responders\HtmlResponder;
use Enyo\Http\Responders\JsonResponder;

return [
    'factories' => [
        HtmlResponder::class => function ($container) {
            return new HtmlResponder(
                $container->get(Session::class),
                $container->get(UrlHelper::class),
                $container->get(Engine::class),
                $container->get(ResponseFactoryInterface::class)
            );
        },

        JsonResponder::class => function ($container) {
            return new JsonResponder(
                $container->get(ResponseFactoryInterface::class)
            );
        },
    ],
];
