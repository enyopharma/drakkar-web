<?php

declare(strict_types=1);

use Services\Efetch;

return [
    Efetch::class => function () {
        return new Efetch;
    },
];
