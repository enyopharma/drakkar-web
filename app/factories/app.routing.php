<?php

declare(strict_types=1);

use App\Routing\UrlGenerator;

return [
    UrlGenerator::class => fn () => new UrlGenerator(
        new FastRoute\RouteParser\Std,
    ),
];
