<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

return [
    RouteCollector::class => fn () => new RouteCollector(
        new FastRoute\RouteParser\Std,
        new FastRoute\DataGenerator\GroupCountBased,
    ),
];
