<?php

declare(strict_types=1);

use Middlewares\Shutdown;
use Middlewares\JsonPayload;

return [
    Shutdown::class => function () {
        return new Shutdown;
    },

    JsonPayload::class => function () {
        return new JsonPayload;
    },
];
