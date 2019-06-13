<?php declare(strict_types=1);

use Psr\Log\LoggerInterface;

use Monolog\Logger;

return [
    'aliases' => [
        LoggerInterface::class => Monolog\Logger::class,
    ],

    'factories' => [
        Logger::class => function () {
            return new Logger('default');
        },
    ],
];
