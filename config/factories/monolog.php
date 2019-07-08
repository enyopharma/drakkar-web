<?php declare(strict_types=1);

use Psr\Log\LoggerInterface;

use Monolog\Logger;

return [
    LoggerInterface::class => function ($container) {
        return $container->get(Monolog\Logger::class);
    },

    Logger::class => function () {
        return new Logger('default');
    },
];
