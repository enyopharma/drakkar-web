<?php declare(strict_types=1);

use Psr\Log\LoggerInterface;

use Enyo\Queue\Client;
use Enyo\Queue\Worker;
use Enyo\Queue\JobHandlerMap;

return [
    'aliases' => [
        'queue.logger' => LoggerInterface::class,
    ],

    'factories' => [
        'queue.priorities' => function () {
            return ['default'];
        },

        Client::class => function ($container) {
            return new Client(
                $container->get(Predis\Client::class),
                ...$container->get('queue.priorities')
            );
        },

        Worker::class => function ($container) {
            return new Worker(
                $container->get(Predis\Client::class),
                $container->get('queue.logger'),
                $container->get(JobHandlerMap::class),
                ...$container->get('queue.priorities')
            );
        },

        JobHandlerMap::class => function ($container) {
            return new JobHandlerMap;
        },
    ],
];
