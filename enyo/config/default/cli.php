<?php declare(strict_types=1);

use App\Cli\Commands\ExampleCommand;

use Symfony\Component\Console\Command\Command;

return [
    'factories' => [
        ExampleCommand::class => function () {
            return new ExampleCommand;
        },
    ],

    'mappers' => [
        'cli.commands' => Command::class,
    ],
];
