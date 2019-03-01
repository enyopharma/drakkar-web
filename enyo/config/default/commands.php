<?php declare(strict_types=1);

return [
    'parameters' => [
        'cli.commands.factory.path' => '%{app.root}/config/commands.php',
    ],

    'factories' => [
        'cli.commands.factory' => function ($container) {
            return require $container->get('cli.commands.factory.path');
        },

        'cli.commands' => function ($container) {
            return $container->get('cli.commands.factory')($container);
        },

        App\Cli\Commands\ExampleCommand::class => function () {
            return new App\Cli\Commands\ExampleCommand;
        },
    ],
];
