<?php declare(strict_types=1);

use Enyo\Http\Session;

return [
    'factories' => [
        Session::class => function () {
            return new Session;
        },
    ],
];
