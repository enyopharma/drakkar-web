<?php declare(strict_types=1);

use Shared\Http\Session;
use Shared\Http\SessionMiddleware;

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
