<?php declare(strict_types=1);

use App\Cli\Commands\CreateHHRunCommand;
use App\Cli\Commands\CreateVHRunCommand;
use App\Cli\Commands\PopulateRunCommand;
use App\Cli\Commands\PopulatePublicationCommand;

return [
    'factories' => [
        CreateHHRunCommand::class => function ($container) {
            return new CreateHHRunCommand(
                $container->get(App\Domain\InsertRun::class)
            );
        },

        CreateVHRunCommand::class => function ($container) {
            return new CreateVHRunCommand(
                $container->get(App\Domain\InsertRun::class)
            );
        },

        PopulateRunCommand::class => function ($container) {
            return new PopulateRunCommand(
                $container->get(App\Domain\PopulateRun::class)
            );
        },

        PopulatePublicationCommand::class => function ($container) {
            return new PopulatePublicationCommand(
                $container->get(App\Domain\PopulatePublication::class)
            );
        },
    ],
];
