<?php declare(strict_types=1);

use Symfony\Component\Console\Command\Command;

use Enyo\Cli\Commands\ExampleCommand;

return [
    'factories' => [
        ExampleCommand::class => function ($container) {
            return new ExampleCommand;
        },
    ],

    'mappers' => [
        'cli.commands' => Command::class,
    ],
];
