<?php declare(strict_types=1);

use Symfony\Component\Console\Application;

return [
    'parameters' => [
        'cli.commands.factory.path' => '%{app.root}/config/commands.php',
    ],

    'factories' => [
        Commands\ExampleCommand::class => function () {
            return new Commands\ExampleCommand;
        },
    ],

    'extensions' => [
        Application::class => function ($container, Application $app) {
            $factory = require $container->get('cli.commands.factory.path');

            $commands = $factory($container);

            foreach ($commands as $command) {
                $app->add($command);
            }

            return $app;
        },
    ],
];
