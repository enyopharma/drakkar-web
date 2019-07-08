<?php declare(strict_types=1);

use App\Cli\Responders\Responder;
use App\Cli\Responders\PopulateResponder;
use App\Http\Responders\HtmlResponder;
use App\Http\Responders\JsonResponder;

return [
    Responder::class => function () {
        return new Responder;
    },

    PopulateResponder::class => function ($container) {
        return new PopulateResponder(
            $container->get(Responder::class)
        );
    },

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
];
