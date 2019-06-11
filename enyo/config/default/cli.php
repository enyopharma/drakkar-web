<?php declare(strict_types=1);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

use Enyo\Cli\Commands\ExampleCommand;

return [
    'mappers' => [
        'cli.commands' => Command::class,
    ],

    'factories' => [
        ExampleCommand::class => function ($container) {
            return new ExampleCommand;
        },
    ],

    'extensions' => [
        Application::class => function ($container, Application $application) {
            $commands = $container->get('cli.commands');

            foreach ($commands as $command) {
                $application->add($command);
            }

            return $application;
        },
    ],
];
