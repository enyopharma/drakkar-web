<?php declare(strict_types=1);

use App\Cli\Responders\Responder;
use App\Cli\Responders\PopulateResponder;

return [
    'factories' => [
        Responder::class => function () {
            return new Responder;
        },

        PopulateResponder::class => function ($container) {
            return new PopulateResponder(
                $container->get(Responder::class)
            );
        },
    ],
];
