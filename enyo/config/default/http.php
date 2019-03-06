<?php declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use Enyo\Http\Session;
use Enyo\Http\Responder;

return [
    'factories' => [
        Responder::class => function ($container) {
            return new Responder(
                $container->get(Session::class),
                $container->get(UrlHelper::class),
                $container->get(Engine::class),
                $container->get(ResponseFactoryInterface::class)
            );
        },
    ],
];
