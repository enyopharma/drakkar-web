<?php declare(strict_types=1);

use Enyo\InstanceFactory;
use Enyo\Cli\CommandFactory;

return [
    'parameters' => [
        'cli.commands.factory.path' => '%{app.root}/config/commands.php',
    ],

    'factories' => [
        CommandFactory::class => function ($container) {
            return new CommandFactory($container, new InstanceFactory($container));
        },

        'cli.commands.factory' => function ($container) {
            return require $container->get('cli.commands.factory.path');
        },

        'cli.commands' => function ($container) {
            return $container->get('cli.commands.factory')(
                $container->get(CommandFactory::class)
            );
        },
    ],
];
