<?php declare(strict_types=1);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Enyo\Queue\JobHandlerMap;
use Enyo\Queue\JobHandlerInterface;

return [
    'parameters' => [
        'queue.logs.path' => '%{app.root}/storage/logs/queue.log',
    ],

    'factories' => [
        'queue.logger' => function ($container) {
            $logger = new Logger('queue');

            $logger->pushHandler(new StreamHandler(
                $container->get('queue.logs.path')
            ));

            return $logger;
        },
    ],

    'mappers' => [
        'queue.job.handlers' => JobHandlerInterface::class,
    ],

    'extensions' => [
        JobHandlerMap::class => function ($container, JobHandlerMap $map) {
            return array_reduce(
                $container->get('queue.job.handlers'),
                function (JobHandlerMap $map, JobHandlerInterface $handler) {
                    return $map->with(get_class($handler), $handler);
                },
                $map
            );
        },
    ]
];
