<?php declare(strict_types=1);

use Symfony\Component\Console\Application;

return [
    'factories' => [
        Application::class => function () {
            return new Application;
        },
    ],

    'extensions' => [
        Application::class => function ($container, Application $app) {
            if ($container->has('cli.commands')) {
                $commands = $container->get('cli.commands');

                foreach ($commands as $command) {
                    $app->add($command);
                }
            }

            return $app;
        },
    ],
];
