<?php

declare(strict_types=1);

use Infrastructure\Efetch;

return [
    Efetch::class => function () {
        return new Efetch;
    },
];
