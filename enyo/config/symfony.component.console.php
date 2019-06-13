<?php declare(strict_types=1);

use Symfony\Component\Console\Application;

return [
    'parameters' => [
        'cli.command.list.factory.path' => '%{app.root}/app/cli.php',
    ],

    'factories' => [
        'cli.command.list.factory' => function ($container) {
            return require $container->get('cli.command.list.factory.path');
        },

        'cli.command.list' => function ($container) {
            return $container->get('cli.command.list.factory')(
                new Enyo\InstanceFactory($container)
            );
        },

        Application::class => function ($container) {
            $application = new Application;

            $commands = $container->get('cli.command.list');

            array_map([$application, 'add'], $commands);

            return $application;
        },
    ],
];
