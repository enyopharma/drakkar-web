<?php declare(strict_types=1);

use Zend\Expressive\Helper\UrlHelper;

return [
    UrlHelper::class => function ($container) {
        return new UrlHelper(
            $container->get(Zend\Expressive\Router\RouterInterface::class)
        );
    },
];
