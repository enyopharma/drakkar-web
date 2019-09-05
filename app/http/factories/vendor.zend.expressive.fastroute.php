<?php

declare(strict_types=1);

use Zend\Expressive\Router\RouterInterface;

return [
    RouterInterface::class => function () {
        return new Zend\Expressive\Router\FastRouteRouter;
    },
];
