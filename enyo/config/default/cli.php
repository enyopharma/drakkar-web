<?php declare(strict_types=1);

use Symfony\Component\Console\Command\Command;

use Enyo\Cli\Responder;
use Enyo\Cli\Commands\ExampleCommand;

return [
    'factories' => [
        Responder::class => function () {
            return new Responder;
        },

        ExampleCommand::class => function ($container) {
            return new ExampleCommand(
                $container->get(Responder::class)
            );
        },
    ],

    'mappers' => [
        'cli.commands' => Command::class,
    ],
];
