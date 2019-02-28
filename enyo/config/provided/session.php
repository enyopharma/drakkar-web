<?php declare(strict_types=1);

use Enyo\Http\Session;
use Enyo\Http\SessionMiddleware;

return [
    'factories' => [
        Session::class => function () {
            return new Session;
        },

        SessionMiddleware::class => function ($container) {
            return new SessionMiddleware(
                $container->get(Session::class)
            );
        },
    ],
];
