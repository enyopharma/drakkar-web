<?php declare(strict_types=1);

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

return [
    'aliases' => [
        RouterInterface::class => FastRouteRouter::class,
    ],

    'factories' => [
        FastRouteRouter::class => function () {
            return new FastRouteRouter;
        },
    ],
];
