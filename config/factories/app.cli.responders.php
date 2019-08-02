<?php

declare(strict_types=1);

use App\Cli\Responders\CliResponder;

return [
    CliResponder::class => function () {
        return new CliResponder;
    },
];
