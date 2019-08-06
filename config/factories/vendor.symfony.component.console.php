<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

return [
    'symfony.components.console.commands' => function ($container) {
        $factory = require sprintf('%s/src/App/Cli/cli.php', $container->get('app.root'));

        return $factory($container);
    },

    Application::class => function ($container) {
        $application = new Application;

        $commands = $container->get('symfony.components.console.commands');

        array_map([$application, 'add'], $commands);

        return $application;
    },
];
