<?php declare(strict_types=1);

return [
    'parameters' => [
        'cli.commands.factory.path' => '%{app.root}/config/commands.php',
    ],

    'factories' => [
        'cli.commands' => function ($container) {
            $commands = require $container->get('cli.commands.factory.path');

            return $commands($container);
        },

        App\Cli\Commands\ExampleCommand::class => function () {
            return new App\Cli\Commands\ExampleCommand;
        },
    ],
];
