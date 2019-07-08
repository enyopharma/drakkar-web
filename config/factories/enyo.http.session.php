<?php declare(strict_types=1);

use Enyo\Http\Session;

return [
    Session::class => function () {
        return new Session;
    },
];
