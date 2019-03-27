<?php declare(strict_types=1);

use App\Cli\Commands\CreateHHRunCommand;
use App\Cli\Commands\CreateVHRunCommand;
use App\Cli\Commands\PopulateRunCommand;
use App\Cli\Commands\PopulatePublicationCommand;

use App\Domain\InsertRun;
use App\Domain\PopulateRun;
use App\Domain\PopulatePublication;

use App\Cli\Responders\Responder;
use App\Cli\Responders\PopulateResponder;

return [
    'factories' => [
        Responder::class => function () {
            return new Responder;
        },

        PopulateResponder::class => function ($container) {
            return new PopulateResponder(
                $container->get(Responder::class)
            );
        },

        CreateHHRunCommand::class => function ($container) {
            return new CreateHHRunCommand(
                $container->get(InsertRun::class),
                $container->get(Responder::class)
            );
        },

        CreateVHRunCommand::class => function ($container) {
            return new CreateVHRunCommand(
                $container->get(InsertRun::class),
                $container->get(Responder::class)
            );
        },

        PopulateRunCommand::class => function ($container) {
            return new PopulateRunCommand(
                $container->get(PopulateRun::class),
                $container->get(PopulateResponder::class)
            );
        },

        PopulatePublicationCommand::class => function ($container) {
            return new PopulatePublicationCommand(
                $container->get(PopulatePublication::class),
                $container->get(PopulateResponder::class)
            );
        },
    ],
];
