<?php declare(strict_types=1);

use Shared\Http\RouteMapper;

return [
    'extensions' => [
        'router.mapper' => function ($container, callable $mapper) {
            return new RouteMapper($container, $mapper);
        },
    ],
];
