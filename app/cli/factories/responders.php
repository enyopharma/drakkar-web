<?php

declare(strict_types=1);

use App\Cli\Responders\RunResponder;
use App\Cli\Responders\PublicationResponder;

return [
    RunResponder::class => function () {
        return new RunResponder;
    },

    PublicationResponder::class => function () {
        return new PublicationResponder;
    },
];
