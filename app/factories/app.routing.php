<?php

declare(strict_types=1);

use Quanta\Http\UrlGenerator;

return [
    UrlGenerator::class => fn () => new UrlGenerator(
        new Quanta\Http\FastRouteUrlPatternParser(
            new FastRoute\RouteParser\Std,
        ),
    ),
];
