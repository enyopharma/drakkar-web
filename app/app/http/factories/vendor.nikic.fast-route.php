<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

return [
    RouteCollector::class => function () {
        return new RouteCollector(
            new FastRoute\RouteParser\Std,
            new FastRoute\DataGenerator\GroupCountBased,
        );
    },

    Dispatcher::class => function ($container) {
        return new FastRoute\Dispatcher\GroupCountBased(
            $container->get(FastRoute\RouteCollector::class)->getData(),
        );
    },
];
