<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

return [
    Application::class => function ($container) {
        $application = new Application;

        $factory = require sprintf('%s/src/App/Cli/cli.php', $container->get('app.root'));

        $commands = $factory(new Enyo\InstanceFactory($container));

        array_map([$application, 'add'], $commands);

        return $application;
    },
];
